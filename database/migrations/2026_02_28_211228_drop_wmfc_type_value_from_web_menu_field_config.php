<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop kolom wmfc_type_value dari web_menu_field_config.
     * 
     * Alasan: Kolom ini tidak pernah digunakan secara fungsional.
     * Runtime ENUM options dibaca langsung dari SHOW COLUMNS (MasterMenuService::getEnumOptions).
     * wmfc_column_type sudah menyimpan full type termasuk ENUM values.
     */
    public function up(): void
    {
        Schema::table('web_menu_field_config', function (Blueprint $table) {
            $table->dropColumn('wmfc_type_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_menu_field_config', function (Blueprint $table) {
            $table->json('wmfc_type_value')
                ->nullable()
                ->after('wmfc_max_length')
                ->comment('(Deprecated) ENUM/SET values sebagai JSON array');
        });
    }
};
