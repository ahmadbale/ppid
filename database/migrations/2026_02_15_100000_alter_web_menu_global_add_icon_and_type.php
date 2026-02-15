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
        Schema::table('web_menu_global', function (Blueprint $table) {
            // Tambah kolom wmg_icon setelah wmg_parent_id
            $table->string('wmg_icon', 50)
                ->nullable()
                ->after('wmg_parent_id')
                ->comment('Icon FontAwesome untuk menu (fa-home, fa-cog, dll). NULL = default fa-circle');
            
            // Tambah kolom wmg_type setelah wmg_icon
            $table->enum('wmg_type', ['general', 'special'])
                ->default('general')
                ->after('wmg_icon')
                ->comment("Type menu: 'general' (sidebar), 'special' (header/modal)");
            
            // Tambah index untuk wmg_type (untuk query performance)
            $table->index('wmg_type', 'idx_wmg_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_menu_global', function (Blueprint $table) {
            $table->dropIndex('idx_wmg_type');
            $table->dropColumn(['wmg_icon', 'wmg_type']);
        });
    }
};
