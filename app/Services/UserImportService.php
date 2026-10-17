<?php

namespace App\Services;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Tim;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserImportService
{
    /**
     * Download template XLSX untuk import user.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Pengguna');

        $headers = [
            'A1' => 'nama',
            'B1' => 'nim',
            'D1' => 'role',
            'E1' => 'password',
            'F1' => 'divisi',
            'G1' => 'jabatan',
            'H1' => 'tim',
            'I1' => 'nomor_hp',
            'J1' => 'jenis_kelamin',
            'K1' => 'tanggal_lahir',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Header style
        $headerRange = 'A1:K1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2563EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Contoh Data Baris 2 - 5
        $sampleRows = [
            [
                'Ahmad Fauzi',
                '2301001',
                'fauzi@example.com',
                'peserta',
                '',
                '',
                '',
                'Axis',
                '081234567890',
                'L',
                '2004-05-15',
            ],
            [
                'Siti Nurhaliza',
                '2301002',
                'siti@example.com',
                'peserta',
                '',
                '',
                '',
                'Vector',
                '081234567891',
                'P',
                '2004-08-20',
            ],
            [
                'Bagas Saputra',
                '2201015',
                'bagas@example.com',
                'panitia',
                '',
                'Ranger',
                'Anggota',
                '',
                '081234567892',
                'L',
                '2003-03-10',
            ],
            [
                'Dian Pramana',
                '2101005',
                'dian@example.com',
                'admin',
                '',
                'Chiper',
                'Anggota',
                '',
                '081234567893',
                'L',
                '2002-11-01',
            ],
        ];

        $rowIndex = 2;
        foreach ($sampleRows as $row) {
            $colIndex = 1;
            foreach ($row as $val) {
                $sheet->setCellValueExplicit(
                    [$colIndex, $rowIndex],
                    $val,
                    DataType::TYPE_STRING
                );
                $colIndex++;
            }
            $rowIndex++;
        }

        // Petunjuk Tambahan di Sheet 2
        $infoSheet = $spreadsheet->createSheet();
        $infoSheet->setTitle('Petunjuk & Referensi');

        $infoSheet->setCellValue('A1', 'Kolom');
        $infoSheet->setCellValue('B1', 'Status');
        $infoSheet->setCellValue('C1', 'Keterangan & Nilai Valid');

        $infoHeaders = 'A1:C1';
        $infoSheet->getStyle($infoHeaders)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $infoSheet->getStyle($infoHeaders)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E293B');

        $guide = [
            ['nama', 'Wajib', 'Nama lengkap pengguna.'],
            ['nim', 'Wajib', 'NIM pengguna, harus unik. Format teks.'],
            ['role', 'Wajib', 'Pilihan: admin, panitia, peserta.'],
            ['password', 'Opsional', 'Jika kosong, default adalah metastro2026.'],
            ['divisi', 'Opsional', 'Nama divisi untuk panitia/admin (misal: Chiper, Ranger, Archivist, Stakeholder, Guider, Pathfinder, dll).'],
            ['jabatan', 'Opsional', 'Jabatan panitia (misal: Ketua, Wakil, Anggota, Pengawas, Person in Charge, dll).'],
            ['tim', 'Opsional', 'Nama tim/kelompok khusus peserta (misal: Axis, Vector, Torque, dll).'],
            ['nomor_hp', 'Opsional', 'Nomor telepon/WhatsApp aktif (contoh: 08123456789).'],
            ['jenis_kelamin', 'Opsional', 'Pilihan: L (Laki-laki) atau P (Perempuan).'],
            ['tanggal_lahir', 'Opsional', 'Format: YYYY-MM-DD (contoh: 2004-05-15).'],
        ];

        $gRow = 2;
        foreach ($guide as $g) {
            $infoSheet->setCellValue("A{$gRow}", $g[0]);
            $infoSheet->setCellValue("B{$gRow}", $g[1]);
            $infoSheet->setCellValue("C{$gRow}", $g[2]);
            $gRow++;
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        foreach (range('A', 'C') as $col) {
            $infoSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'template-import-users.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import users dari file XLSX atau CSV.
     *
     * @return array{created_count: int, skipped_count: int, errors: array<int, string>}
     */
    public function import(UploadedFile $file): array
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) <= 1) {
            return [
                'created_count' => 0,
                'skipped_count' => 0,
                'errors' => ['File spreadsheet kosong atau hanya berisi header.'],
            ];
        }

        // Temukan index header di row 1
        $headerRow = array_shift($rows);
        $headerMap = [];
        foreach ($headerRow as $colLetter => $headerName) {
            $cleanName = strtolower(trim((string) $headerName));
            if ($cleanName !== '') {
                $headerMap[$cleanName] = $colLetter;
            }
        }

        $requiredHeaders = ['nama', 'nim', 'role'];
        foreach ($requiredHeaders as $req) {
            if (! isset($headerMap[$req])) {
                return [
                    'created_count' => 0,
                    'skipped_count' => 0,
                    'errors' => ["Header kolom '{$req}' tidak ditemukan dalam file. Pastikan menggunakan format template yang disediakan."],
                ];
            }
        }

        $divisis = Divisi::all()->keyBy(fn ($d) => strtolower($d->nama));
        $jabatans = Jabatan::all()->keyBy(fn ($j) => strtolower($j->nama));
        $tims = Tim::all()->keyBy(fn ($t) => strtolower($t->nama));

        $createdCount = 0;
        $skippedCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            $rowNumber = 1;
            foreach ($rows as $row) {
                $rowNumber++;

                $nama = trim((string) ($row[$headerMap['nama']] ?? ''));
                $nim = trim((string) ($row[$headerMap['nim']] ?? ''));
                $role = strtolower(trim((string) ($row[$headerMap['role']] ?? '')));

                // Lewati baris kosong total
                if ($nama === '' && $nim === '') {
                    continue;
                }

                if ($nama === '' || $nim === '' || $role === '') {
                    $errors[] = "Baris {$rowNumber}: Data tidak lengkap (Nama, NIM, dan Role wajib diisi).";
                    $skippedCount++;

                    continue;
                }

                if (! in_array($role, ['admin', 'panitia', 'peserta'], true)) {
                    $errors[] = "Baris {$rowNumber}: Role '{$role}' tidak valid. Harus salah satu dari: admin, panitia, peserta.";
                    $skippedCount++;

                    continue;
                }

                // Cek duplikasi NIM atau Email
                if (User::where('nim', $nim)->exists()) {
                    $errors[] = "Baris {$rowNumber}: Pengguna dengan NIM '{$nim}' sudah terdaftar di sistem.";
                    $skippedCount++;

                    continue;
                }

                // Password
                $rawPassword = isset($headerMap['password']) ? trim((string) ($row[$headerMap['password']] ?? '')) : '';
                $password = $rawPassword !== '' ? $rawPassword : 'metastro2026';
                $mustChangePassword = $rawPassword === '';

                // Divisi & Jabatan
                $divisiName = isset($headerMap['divisi']) ? strtolower(trim((string) ($row[$headerMap['divisi']] ?? ''))) : '';
                $jabatanName = isset($headerMap['jabatan']) ? strtolower(trim((string) ($row[$headerMap['jabatan']] ?? ''))) : '';
                $timName = isset($headerMap['tim']) ? strtolower(trim((string) ($row[$headerMap['tim']] ?? ''))) : '';
                $nomorHp = isset($headerMap['nomor_hp']) ? trim((string) ($row[$headerMap['nomor_hp']] ?? '')) : null;
                $jenisKelamin = isset($headerMap['jenis_kelamin']) ? strtoupper(trim((string) ($row[$headerMap['jenis_kelamin']] ?? ''))) : null;
                $tanggalLahirRaw = isset($headerMap['tanggal_lahir']) ? trim((string) ($row[$headerMap['tanggal_lahir']] ?? '')) : null;

                $divisiId = null;
                $jabatanId = null;

                if ($role === 'admin') {
                    $chiperDiv = $divisis->get('chiper');
                    $divisiId = $chiperDiv?->id;
                    $jabatanId = $jabatans->get($jabatanName)?->id ?? $jabatans->get('anggota')?->id;
                } elseif ($role === 'panitia') {
                    if ($divisiName !== '') {
                        $divisiId = $divisis->get($divisiName)?->id;
                    }
                    if ($jabatanName !== '') {
                        $jabatanId = $jabatans->get($jabatanName)?->id;
                    }
                }

                $tanggalLahir = null;
                if ($tanggalLahirRaw) {
                    try {
                        if (is_numeric($tanggalLahirRaw)) {
                            $tanggalLahir = Carbon::instance(ExcelDate::excelToDateTimeObject((float) $tanggalLahirRaw))->toDateString();
                        } else {
                            $tanggalLahir = Carbon::parse($tanggalLahirRaw)->toDateString();
                        }
                    } catch (\Throwable) {
                        $tanggalLahir = null;
                    }
                }

                $jk = in_array($jenisKelamin, ['L', 'P'], true) ? $jenisKelamin : null;

                $user = User::create([
                    'nama' => $nama,
                    'nim' => $nim,
                    'password' => $password,
                    'nomor_hp' => $nomorHp ?: null,
                    'jenis_kelamin' => $jk,
                    'tanggal_lahir' => $tanggalLahir,
                    'divisi_id' => $divisiId,
                    'jabatan_id' => $jabatanId,
                    'status' => true,
                    'must_change_password' => $mustChangePassword,
                    'is_initial_setup_completed' => false,
                    'qr_token' => (string) Str::uuid(),
                    'qr_updated_at' => now(),
                ]);

                $user->assignRole($role);

                // Tim untuk peserta
                if ($role === 'peserta' && $timName !== '') {
                    $tim = $tims->get($timName);
                    if (! $tim) {
                        $tim = Tim::firstOrCreate(
                            ['slug' => Str::slug($timName)],
                            ['nama' => ucwords($timName)]
                        );
                        $tims->put($timName, $tim);
                    }

                    $user->tims()->create(['tim_id' => $tim->id]);
                }

                $createdCount++;
            }

            DB::commit();

            return [
                'created_count' => $createdCount,
                'skipped_count' => $skippedCount,
                'errors' => $errors,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'created_count' => 0,
                'skipped_count' => 0,
                'errors' => ['Terjadi kesalahan internal saat memproses file: '.$e->getMessage()],
            ];
        }
    }
}
