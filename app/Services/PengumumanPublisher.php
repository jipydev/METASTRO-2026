<?php

namespace App\Services;

use App\Models\Pengumuman;
use Carbon\Carbon;

class PengumumanPublisher
{
    public function __construct(private NotificationDispatcher $notifications) {}

    /**
     * Publikasikan draft yang jadwal rilisnya sudah lewat.
     */
    public function publishDue(): int
    {
        $count = 0;

        Pengumuman::query()
            ->where('status', 'draft')
            ->whereNotNull('tanggal_publish')
            ->where('tanggal_publish', '<=', now())
            ->each(function (Pengumuman $pengumuman) use (&$count): void {
                $pengumuman->update(['status' => 'published']);
                $this->notifications->pengumumanPublished($pengumuman, $pengumuman->pembuat_id);
                $count++;
            });

        return $count;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    public function resolveAttributes(array $validated): array
    {
        $validated['status'] = $this->normalizeStatus($validated['status'] ?? 'draft');

        if ($validated['status'] === 'published') {
            $validated['tanggal_publish'] = now();

            return $validated;
        }

        $validated['tanggal_publish'] = Carbon::parse($validated['tanggal_publish']);

        if ($validated['tanggal_publish']->lte(now())) {
            $validated['status'] = 'published';
        }

        return $validated;
    }

    public function normalizeStatus(string $status): string
    {
        $status = strtolower(trim($status));

        return in_array($status, ['publish', 'published'], true) ? 'published' : 'draft';
    }
}
