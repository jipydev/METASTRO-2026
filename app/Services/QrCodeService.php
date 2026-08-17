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
     * Generate QR token and QR code image for a user.
     */
    public function generateForUser(User $user): string
    {
        if (empty($user->qr_token)) {
            $user->update(['qr_token' => Str::uuid()->toString(), 'qr_updated_at' => now()]);
        }

        return $this->generateQrImage($user);
    }

    /**
     * Re-generate QR token and QR code image (invalidates old QR).
     */
    public function regenerateForUser(User $user): string
    {
        $this->deleteQrImage($user);

        $user->update(['qr_token' => Str::uuid()->toString(), 'qr_updated_at' => now()]);

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
            return Storage::url($filename);
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
}
