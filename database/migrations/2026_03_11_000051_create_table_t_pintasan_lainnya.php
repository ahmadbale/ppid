<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_pintasan_lainnya', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('pintasan_lainnya_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_kategori_akses')->unsigned()->nullable();
            $table->string('tpl_nama_kategori', 100);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_pintasan_lainnya_isDeleted');
            $table->index('fk_m_kategori_akses', 't_pintasan_lainnya_fk_m_kategori_akses_idx');

            $table->foreign('fk_m_kategori_akses', 'fk_t_pintasan_lainnya_m_kategori_akses')
                ->references('kategori_akses_id')->on('m_kategori_akses')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_pintasan_lainnya', function (Blueprint $table) {
            $table->dropForeign('fk_t_pintasan_lainnya_m_kategori_akses');
        });

        Schema::dropIfExists('t_pintasan_lainnya');
    }
};
