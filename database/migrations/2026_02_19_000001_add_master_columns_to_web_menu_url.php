<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan kolom wmu_kategori_menu dan wmu_akses_tabel 
     * untuk mendukung fitur Template Master (Menu Tanpa Ngoding)
     */
    public function up(): void
    {
        Schema::table('web_menu_url', function (Blueprint $table) {
            // Kolom wmu_kategori_menu - kategori menu URL
            $table->string('wmu_kategori_menu', 50)
                ->default('custom')
                ->after('wmu_keterangan')
                ->comment('Kategori menu: master (template CRUD), pengajuan (template approval), custom (manual coding)');
            
            // Kolom wmu_akses_tabel - nama tabel yang diakses
            $table->string('wmu_akses_tabel', 100)
                ->nullable()
                ->after('wmu_kategori_menu')
                ->comment('Nama tabel yang diakses (hanya untuk kategori master/pengajuan)');
            
            // Index untuk performa query
            $table->index('wmu_kategori_menu', 'idx_wmu_kategori_menu');
            $table->index('wmu_akses_tabel', 'idx_wmu_akses_tabel');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Menghapus kolom yang ditambahkan
     */
    public function down(): void
    {
        Schema::table('web_menu_url', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('idx_wmu_kategori_menu');
            $table->dropIndex('idx_wmu_akses_tabel');
            
            // Drop columns
            $table->dropColumn(['wmu_kategori_menu', 'wmu_akses_tabel']);
        });
    }
};
