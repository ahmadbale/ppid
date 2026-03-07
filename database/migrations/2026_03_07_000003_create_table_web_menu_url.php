<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_menu_url', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('web_menu_url_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_application')->unsigned();
            $table->integer('wmu_parent_id')->unsigned()->nullable();
            $table->string('wmu_nama', 255)->nullable();
            $table->string('controller_name', 100)->nullable();
            $table->enum('module_type', ['sisfo', 'user'])->default('sisfo');
            $table->string('wmu_keterangan', 255)->nullable();
            $table->string('wmu_kategori_menu', 50)->default('custom');
            $table->string('wmu_akses_tabel', 100)->nullable();
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('fk_m_application', 'fk_web_menu_url_m_application1_idx');
            $table->index('isDeleted', 'web_menu_url_isDeleted');
            $table->index('wmu_parent_id', 'idx_wmu_parent_id');
            $table->index('controller_name', 'idx_controller_name');
            $table->index('module_type', 'idx_module_type');
            $table->index('wmu_kategori_menu', 'idx_wmu_kategori_menu');
            $table->index('wmu_akses_tabel', 'idx_wmu_akses_tabel');

            $table->foreign('fk_m_application', 'fk_web_menu_url_m_application1')
                ->references('application_id')->on('m_application')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('web_menu_url', function (Blueprint $table) {
            $table->dropForeign('fk_web_menu_url_m_application1');
        });

        Schema::dropIfExists('web_menu_url');
    }
};
