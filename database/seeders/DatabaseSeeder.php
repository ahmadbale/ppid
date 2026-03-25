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
            \Database\Seeders\Seeder000003WebMenuUrl::class,
            \Database\Seeders\Seeder000004WebMenuGlobal::class,
            \Database\Seeders\Seeder000005WebMenu::class,
            \Database\Seeders\Seeder000006SetHakAkses::class,
            \Database\Seeders\Seeder000007SetUserHakAkses::class,
            \Database\Seeders\Seeder000009MApplication::class,
            \Database\Seeders\Seeder000026MKategoriForm::class,
            \Database\Seeders\Seeder000029MBeritaDinamis::class,
            \Database\Seeders\Seeder000031MIpDinamisTabel::class,
            \Database\Seeders\Seeder000032MKategoriAkses::class,
            \Database\Seeders\Seeder000033MKategoriFooter::class,
            \Database\Seeders\Seeder000034MLhkpn::class,
            \Database\Seeders\Seeder000036MMediaDinamis::class,
            \Database\Seeders\Seeder000037MPengumumanDinamis::class,
            \Database\Seeders\Seeder000039MRegulasiDinamis::class,
            \Database\Seeders\Seeder000040TAksesCepat::class,
            \Database\Seeders\Seeder000042TDetailLhkpn::class,
            \Database\Seeders\Seeder000043TDetailMediaDinamis::class,
            \Database\Seeders\Seeder000046TFooter::class,
            \Database\Seeders\Seeder000051TPintasanLainnya::class,
            \Database\Seeders\Seeder000052TPengumuman::class,
            \Database\Seeders\Seeder000054TKategoriRegulasi::class,
        ]);
    }
}
