<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('web_menu_field_config', function (Blueprint $table) {
            // Add wmfc_type_value - Store ENUM/SET values (comma separated)
            $table->text('wmfc_type_value')
                ->nullable()
                ->after('wmfc_max_length')
                ->comment('ENUM/SET values (comma separated) - e.g., "DISETUJUI,DITOLAK,MENUNGGU"');
            
            // Add wmfc_label_keterangan - Help text below field
            $table->string('wmfc_label_keterangan', 500)
                ->nullable()
                ->after('wmfc_type_value')
                ->comment('Help text/keterangan yang ditampilkan di bawah field input');
            
            // Add wmfc_ukuran_max - Max file size (KB) for file/image upload
            $table->integer('wmfc_ukuran_max')
                ->nullable()
                ->after('wmfc_label_keterangan')
                ->comment('Max file size dalam KB untuk field type file/image (e.g., 2048 = 2MB)');
            
            // Add wmfc_display_list - Toggle show/hide in list view
            $table->tinyInteger('wmfc_display_list')
                ->default(1)
                ->after('wmfc_ukuran_max')
                ->comment('1 = tampil di list view, 0 = hidden (hanya di detail/form)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_menu_field_config', function (Blueprint $table) {
            $table->dropColumn([
                'wmfc_type_value',
                'wmfc_label_keterangan',
                'wmfc_ukuran_max',
                'wmfc_display_list',
            ]);
        });
    }
};
