<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tambah kolom controller_name dan module_type untuk dynamic routing system
    public function up(): void
    {
        Schema::table('web_menu_url', function (Blueprint $table) {
            // Kolom controller_name - nama controller untuk routing
            $table->string('controller_name', 100)
                ->nullable()
                ->after('wmu_nama')
                ->comment('Nama controller (relative path dari Modules\Sisfo\App\Http\Controllers)');
            
            // Kolom module_type - sisfo (dynamic) atau user (static)
            $table->enum('module_type', ['sisfo', 'user'])
                ->default('sisfo')
                ->after('controller_name')
                ->comment('Module handler: sisfo (dynamic routing) atau user (static routing)');
            
            // Index untuk performa query
            $table->index('controller_name', 'idx_controller_name');
            $table->index('module_type', 'idx_module_type');
        });
    }

    // Rollback: hapus kolom controller_name dan module_type
    public function down(): void
    {
        Schema::table('web_menu_url', function (Blueprint $table) {
            $table->dropIndex('idx_controller_name');
            $table->dropIndex('idx_module_type');
            $table->dropColumn(['controller_name', 'module_type']);
        });
    }
};
