<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_menu', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('web_menu_id')->unsigned()->autoIncrement();
            $table->integer('fk_web_menu_global')->unsigned();
            $table->integer('fk_m_hak_akses')->unsigned();
            $table->integer('wm_parent_id')->unsigned()->nullable();
            $table->integer('wm_urutan_menu')->default(0);
            $table->string('wm_menu_nama', 255)->nullable();
            $table->enum('wm_status_menu', ['aktif', 'nonaktif'])->default('aktif');
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 'web_menu_isDeleted');
            $table->index('wm_parent_id', 'fk_web_menu_idx');
            $table->index('fk_web_menu_global', 'fk_web_menu_web_menu_global1_idx');
            $table->index('fk_m_hak_akses', 'fk_web_menu_m_hak_akses1_idx');

            $table->foreign('fk_m_hak_akses', 'fk_web_menu_m_hak_akses1')
                ->references('hak_akses_id')->on('m_hak_akses')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');

            $table->foreign('fk_web_menu_global', 'fk_web_menu_web_menu_global1')
                ->references('web_menu_global_id')->on('web_menu_global')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('web_menu', function (Blueprint $table) {
            $table->dropForeign('fk_web_menu_m_hak_akses1');
            $table->dropForeign('fk_web_menu_web_menu_global1');
        });

        Schema::dropIfExists('web_menu');
    }
};
