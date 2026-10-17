<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QrCodeService
{
    /**
     * Generate unique attendance code for user.
     */
    public function generateUniqueCode(): string
    {
        do {
            $code = 'MTR-'.strtoupper(Str::random(4));
        } while (User::where('kode_unik', $code)->exists());

        return $code;
    }

    /**
     * Generate QR token and QR code image for a user.
     */
    public function generateForUser(User $user): string
    {
        $updates = [];
        if (empty($user->qr_token)) {
            $updates['qr_token'] = Str::uuid()->toString();
        }
        if (empty($user->kode_unik)) {
            $updates['kode_unik'] = $this->generateUniqueCode();
        }
        if (! empty($updates) || ! $user->qr_updated_at) {
            $updates['qr_updated_at'] = now();
            $user->update($updates);
        }

        return $this->generateQrImage($user);
    }

    /**
     * Generate the QR code SVG image and save to storage.
     */
    protected function generateQrImage(User $user): string
    {
        $qrData = json_encode([
            'user_id' => $user->id,
            'token' => $user->qr_token,
            'kode_unik' => $user->kode_unik,
        ]);

        $renderer = new ImageRenderer(
            new RendererStyle(300, 2),
            new SvgImageBackEnd
        );

        $writer = new Writer($renderer);
        $svgContent = $writer->writeString($qrData);

        $filename = "qrcodes/user_{$user->id}.svg";
        Storage::disk('public')->put($filename, $svgContent);

        return $filename;
    }

    /**
     * Delete existing QR code image.
     */
    protected function deleteQrImage(User $user): void
    {
        $filename = "qrcodes/user_{$user->id}.svg";

        if (Storage::disk('public')->exists($filename)) {
            Storage::disk('public')->delete($filename);
        }
    }

    /**
     * Get the public URL of a user's QR code.
     */
    public function getQrUrl(User $user): ?string
    {
        $filename = "qrcodes/user_{$user->id}.svg";

        if (Storage::disk('public')->exists($filename)) {
            $version = $user->qr_updated_at?->timestamp
                ?? Storage::disk('public')->lastModified($filename);

            return Storage::url($filename).'?v='.$version;
        }

        return null;
    }

    /**
     * Lookup user by QR token (used by scanner).
     */
    public function lookupByToken(string $token): ?User
    {
        return User::where('qr_token', $token)
            ->where('status', true)
            ->with(['divisi', 'jabatan'])
            ->first();
    }

    /**
     * Lookup user by unique attendance code or NIM (used for manual entry).
     */
    public function lookupByKodeUnik(string $code): ?User
    {
        $cleaned = strtoupper(trim($code));

        if (empty($cleaned)) {
            return null;
        }

        $variations = [$cleaned];
        if (! str_starts_with($cleaned, 'MTR-')) {
            $variations[] = 'MTR-'.$cleaned;
        }
        if (str_starts_with($cleaned, 'MTR') && ! str_starts_with($cleaned, 'MTR-')) {
            $variations[] = 'MTR-'.substr($cleaned, 3);
        }

        return User::where('status', true)
            ->where(function ($query) use ($cleaned, $variations) {
                $query->whereIn('kode_unik', $variations)
                    ->orWhere('nim', $cleaned);
            })
            ->with(['divisi', 'jabatan'])
            ->first();
    }
}
