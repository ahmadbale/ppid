<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeders yang dibuat dengan penamaan sesuai kode migration
        $this->call([
            \Database\Seeders\Seeder000001MHakAkses::class,
            \Database\Seeders\Seeder000002MUser::class,
            \Database\Seeders\Seeder000006SetHakAkses::class,
            \Database\Seeders\Seeder000007SetUserHakAkses::class,
        ]);
    }
}
