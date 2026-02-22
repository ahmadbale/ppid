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
            // Add column type (VARCHAR(200), TEXT, INT, etc)
            $table->string('wmfc_column_type', 100)->nullable()->after('wmfc_column_name')
                ->comment('Tipe data kolom dengan length (VARCHAR(200), TEXT, INT, etc)');
            
            // Add max length from column definition
            $table->integer('wmfc_max_length')->nullable()->after('wmfc_fk_display_columns')
                ->comment('Maximum length dari column definition (untuk validasi)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_menu_field_config', function (Blueprint $table) {
            $table->dropColumn(['wmfc_column_type', 'wmfc_max_length']);
        });
    }
};
