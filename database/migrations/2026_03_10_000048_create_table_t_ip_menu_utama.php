<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ip_menu_utama', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('ip_menu_utama_id')->unsigned()->autoIncrement();
            $table->integer('fk_t_ip_dinamis_tabel')->unsigned()->nullable();
            $table->string('nama_ip_mu', 200)->nullable();
            $table->string('dokumen_ip_mu', 100)->nullable();
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('fk_t_ip_dinamis_tabel', 'fk_t_ip_menu_utama_t_ip_dinamis1_idx');
            $table->index('isDeleted', 't_ip_menu_utama_isDeleted');

            $table->foreign('fk_t_ip_dinamis_tabel', 't_ip_menu_utama_fk_t_ip_dinamis_tabel')
                ->references('ip_dinamis_tabel_id')->on('m_ip_dinamis_tabel')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_ip_menu_utama', function (Blueprint $table) {
            $table->dropForeign('t_ip_menu_utama_fk_t_ip_dinamis_tabel');
        });

        Schema::dropIfExists('t_ip_menu_utama');
    }
};
