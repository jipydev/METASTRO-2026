<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use ZipArchive;

class AttendanceImportParser
{
    /**
     * @return list<array{row: int, nim: string, status: string, waktu: mixed}>
     */
    public function parse(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        if ($path === false) {
            throw new InvalidArgumentException('Berkas impor tidak dapat dibaca.');
        }

        if ($extension === 'xls') {
            throw new InvalidArgumentException('Format .xls tidak didukung. Simpan sebagai .xlsx atau CSV lalu unggah ulang.');
        }

        $matrix = $extension === 'xlsx'
            ? $this->readXlsx($path)
            : $this->readCsv($path);

        return $this->mapRows($matrix);
    }

    /**
     * @return list<list<mixed>>
     */
    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new InvalidArgumentException('Berkas CSV tidak dapat dibuka.');
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);

            return [];
        }

        $firstLine = $this->stripBom($firstLine);
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        rewind($handle);
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $rows = [];
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($this->isEmptyRow($data)) {
                continue;
            }

            $rows[] = $data;
        }

        fclose($handle);

        return $rows;
    }

    /**
     * @return list<list<mixed>>
     */
    private function readXlsx(string $path): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw new InvalidArgumentException('Impor Excel membutuhkan ekstensi PHP zip. Simpan berkas sebagai CSV lalu unggah ulang.');
        }

        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new InvalidArgumentException('Berkas Excel tidak dapat dibuka.');
        }

        $sharedStrings = $this->parseSharedStrings((string) $zip->getFromName('xl/sharedStrings.xml'));
        $sheetXml = $this->firstWorksheetXml($zip);
        $zip->close();

        if ($sheetXml === null) {
            throw new InvalidArgumentException('Lembar kerja Excel tidak ditemukan.');
        }

        return $this->parseSheetXml($sheetXml, $sharedStrings);
    }

    private function firstWorksheetXml(ZipArchive $zip): ?string
    {
        $candidates = ['xl/worksheets/sheet1.xml', 'xl/worksheets/sheet.xml'];

        foreach ($candidates as $name) {
            $xml = $zip->getFromName($name);
            if (is_string($xml) && $xml !== '') {
                return $xml;
            }
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (is_string($name) && str_starts_with($name, 'xl/worksheets/sheet') && str_ends_with($name, '.xml')) {
                $xml = $zip->getFromName($name);
                if (is_string($xml) && $xml !== '') {
                    return $xml;
                }
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function parseSharedStrings(string $xml): array
    {
        if ($xml === '') {
            return [];
        }

        $previous = libxml_use_internal_errors(true);
        $document = simplexml_load_string($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($document === false) {
            return [];
        }

        $strings = [];

        foreach ($this->spreadsheetChildren($document)->si as $item) {
            $strings[] = trim(html_entity_decode(strip_tags($item->asXML() ?: ''), ENT_QUOTES | ENT_XML1, 'UTF-8'));
        }

        return $strings;
    }

    /**
     * @param  list<string>  $sharedStrings
     * @return list<list<mixed>>
     */
    private function parseSheetXml(string $xml, array $sharedStrings): array
    {
        $previous = libxml_use_internal_errors(true);
        $document = simplexml_load_string($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($document === false) {
            throw new InvalidArgumentException('Isi lembar Excel tidak valid.');
        }

        $rows = [];
        $sheetData = $this->spreadsheetChildren($document)->sheetData;

        foreach ($this->spreadsheetChildren($sheetData)->row as $row) {
            $cells = [];

            foreach ($this->spreadsheetChildren($row)->c as $cell) {
                $reference = (string) $cell['r'];
                $index = $this->columnIndex($reference);
                $type = (string) $cell['t'];
                $value = $this->cellValue($cell, $type, $sharedStrings);

                if ($index !== null) {
                    $cells[$index] = $value;
                }
            }

            if ($cells === []) {
                continue;
            }

            $max = max(array_keys($cells));
            $normalized = [];
            for ($i = 0; $i <= $max; $i++) {
                $normalized[] = $cells[$i] ?? null;
            }

            if (! $this->isEmptyRow($normalized)) {
                $rows[] = $normalized;
            }
        }

        return $rows;
    }

    /**
     * @param  list<string>  $sharedStrings
     */
    private function cellValue(\SimpleXMLElement $cell, string $type, array $sharedStrings): mixed
    {
        $children = $this->spreadsheetChildren($cell);

        if ($type === 's') {
            $index = (int) $children->v;

            return $sharedStrings[$index] ?? '';
        }

        if ($type === 'inlineStr') {
            $inline = $children->is;

            return trim((string) $this->spreadsheetChildren($inline)->t);
        }

        $raw = trim((string) $children->v);

        if ($raw !== '' && is_numeric($raw)) {
            return str_contains($raw, '.') ? (float) $raw : (int) $raw;
        }

        return $raw;
    }

    private function columnIndex(string $reference): ?int
    {
        if (! preg_match('/^([A-Z]+)/i', $reference, $matches)) {
            return null;
        }

        $letters = strtoupper($matches[1]);
        $index = 0;

        for ($i = 0; $i < strlen($letters); $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }

        return $index - 1;
    }

    /**
     * @param  list<list<mixed>>  $matrix
     * @return list<array{row: int, nim: string, status: string, waktu: mixed}>
     */
    private function mapRows(array $matrix): array
    {
        if ($matrix === []) {
            throw new InvalidArgumentException('Berkas impor kosong.');
        }

        $header = array_map(fn (mixed $value): string => $this->normalizeHeader($value), $matrix[0]);
        $indexes = $this->resolveIndexes($header);
        $start = $indexes === null ? 0 : 1;

        if ($indexes === null) {
            $indexes = ['nim' => 0, 'status' => null, 'waktu' => 1];
        }

        $mapped = [];
        for ($i = $start; $i < count($matrix); $i++) {
            $row = $matrix[$i];
            $nim = $this->normalizeNim($row[$indexes['nim']] ?? null);

            if ($nim === '') {
                continue;
            }

            $mapped[] = [
                'row' => $i + 1,
                'nim' => $nim,
                'status' => $this->normalizeStatus(
                    $indexes['status'] !== null ? ($row[$indexes['status']] ?? null) : null
                ),
                'waktu' => $indexes['waktu'] !== null ? ($row[$indexes['waktu']] ?? null) : null,
            ];
        }

        if ($mapped === []) {
            throw new InvalidArgumentException('Tidak ada baris dengan kolom NIM yang dapat diimpor.');
        }

        return $mapped;
    }

    /**
     * @param  list<string>  $header
     * @return array{nim: int, status: int|null, waktu: int|null}|null
     */
    private function resolveIndexes(array $header): ?array
    {
        $nim = $this->findColumn($header, ['nim', 'nip', 'nrp']);
        if ($nim === null) {
            return null;
        }

        return [
            'nim' => $nim,
            'status' => $this->findColumn($header, ['status', 'kehadiran']),
            'waktu' => $this->findColumn($header, ['waktu', 'jam', 'jam tap', 'jam_tap', 'time']),
        ];
    }

    /**
     * @param  list<string>  $header
     * @param  list<string>  $aliases
     */
    private function findColumn(array $header, array $aliases): ?int
    {
        foreach ($header as $index => $name) {
            if (in_array($name, $aliases, true)) {
                return $index;
            }
        }

        return null;
    }

    public function normalizeNim(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_int($value)) {
            return (string) $value;
        }

        if (is_float($value)) {
            return (string) (int) $value;
        }

        $nim = trim((string) $value);
        $nim = preg_replace('/\.0+$/', '', $nim) ?? $nim;

        return trim($nim);
    }

    public function normalizeStatus(mixed $value): string
    {
        $status = strtolower(trim((string) ($value ?? '')));

        return match ($status) {
            '', 'hadir', 'h', 'present', 'terlambat', 'telat' => 'hadir',
            'izin', 'i' => 'izin',
            'sakit', 's' => 'sakit',
            'alpa', 'alpha', 'a', 'absen' => 'alpa',
            default => $status,
        };
    }

    /**
     * @param  list<mixed>  $row
     */
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if ($cell !== null && trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeHeader(mixed $value): string
    {
        $header = strtolower(trim((string) $value));
        $header = str_replace(['_', '-'], ' ', $header);

        return preg_replace('/\s+/', ' ', $header) ?? $header;
    }

    private function stripBom(string $value): string
    {
        return str_starts_with($value, "\xEF\xBB\xBF") ? substr($value, 3) : $value;
    }

    private function spreadsheetChildren(\SimpleXMLElement $element): \SimpleXMLElement
    {
        $namespaced = $element->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        return $namespaced->count() > 0 ? $namespaced : $element->children();
    }
}
