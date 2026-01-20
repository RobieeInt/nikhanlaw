<?php

namespace App\Support;

use Illuminate\Support\Str;

class CaseNumber
{
    public static function make(): string
    {
        // contoh: NIK-2026-000123-AB12
        $year = now()->format('Y');
        $rand = Str::upper(Str::random(4));
        $seq = str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);

        return "NIK-{$year}-{$seq}-{$rand}";
    }
}
