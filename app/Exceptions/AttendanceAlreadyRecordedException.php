<?php

namespace App\Exceptions;

use RuntimeException;

class AttendanceAlreadyRecordedException extends RuntimeException
{
    public function __construct(string $kegiatanNama)
    {
        parent::__construct("Pengguna ini sudah tercatat pada kegiatan '{$kegiatanNama}'.");
    }
}
