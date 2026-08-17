<?php

namespace App\Console\Commands;

use App\Services\PengumumanPublisher;
use Illuminate\Console\Command;

class PublishScheduledPengumumans extends Command
{
    protected $signature = 'pengumuman:publish-scheduled';

    protected $description = 'Publikasikan pengumuman draft yang sudah mencapai tanggal rilis';

    public function handle(PengumumanPublisher $publisher): int
    {
        $count = $publisher->publishDue();

        if ($count > 0) {
            $this->info("{$count} pengumuman berhasil dipublikasikan.");
        }

        return self::SUCCESS;
    }
}
