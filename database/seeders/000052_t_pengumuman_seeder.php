<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Seeder000052TPengumuman extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Tabel t_pengumuman kosong (tidak ada data di SQL)
        $data = [];

        DB::table('t_pengumuman')->insertOrIgnore($data);
    }
}
