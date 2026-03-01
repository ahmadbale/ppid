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
            $table->string('wmfc_fk_priority_display', 100)
                ->nullable()
                ->after('wmfc_fk_label_columns')
                ->comment('Nama kolom FK yang diprioritaskan untuk ditampilkan di index/detail/edit/delete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_menu_field_config', function (Blueprint $table) {
            $table->dropColumn('wmfc_fk_priority_display');
        });
    }
};
