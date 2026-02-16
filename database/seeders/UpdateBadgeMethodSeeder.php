<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateBadgeMethodSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('web_menu_global')
            ->whereHas('WebMenuUrl', function($q) {
                $q->where('wmu_nama', 'notifikasi-masuk')
                  ->where('module_type', 'sisfo');
            })
            ->update(['wmg_badge_method' => 'getBadgeCount']);

        $this->command->info('Badge method updated successfully for notifikasi-masuk menu!');
    }
}
