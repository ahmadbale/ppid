<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * 1. Ubah wmfc_type_value dari TEXT menjadi JSON (array nilai ENUM/SET)
     * 2. Tambah wmfc_fk_label_columns (JSON) - alias label kolom pada modal FK search
     */
    public function up(): void
    {
        // Step 1: Migrate existing wmfc_type_value data dari comma-string ke JSON array
        // Sebelum alter column agar data tidak hilang
        $records = DB::table('web_menu_field_config')
            ->whereNotNull('wmfc_type_value')
            ->where('wmfc_type_value', '!=', '')
            ->get(['web_menu_field_config_id', 'wmfc_type_value']);

        foreach ($records as $record) {
            $raw = $record->wmfc_type_value;

            // Skip jika sudah JSON array
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                continue; // Sudah JSON, skip
            }

            // Konversi dari comma-separated string ke JSON array
            $values = array_map('trim', explode(',', $raw));
            DB::table('web_menu_field_config')
                ->where('web_menu_field_config_id', $record->web_menu_field_config_id)
                ->update(['wmfc_type_value' => json_encode($values)]);
        }

        Schema::table('web_menu_field_config', function (Blueprint $table) {
            // Ubah wmfc_type_value menjadi JSON
            $table->json('wmfc_type_value')
                ->nullable()
                ->change()
                ->comment('ENUM/SET values sebagai JSON array - e.g., ["DISETUJUI","DITOLAK"]');

            // Tambah wmfc_fk_label_columns - alias label kolom pada modal FK search
            $table->json('wmfc_fk_label_columns')
                ->nullable()
                ->after('wmfc_fk_display_columns')
                ->comment('Alias label untuk kolom di modal FK search - e.g., ["Kode","Nama"]. Jika null/default, gunakan nama kolom asli.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_menu_field_config', function (Blueprint $table) {
            // Kembalikan wmfc_type_value ke TEXT
            $table->text('wmfc_type_value')
                ->nullable()
                ->change()
                ->comment('ENUM/SET values (comma separated)');

            $table->dropColumn('wmfc_fk_label_columns');
        });
    }
};
