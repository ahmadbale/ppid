<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_regulasi', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('regulasi_id')->unsigned()->autoIncrement();
            $table->integer('fk_t_kategori_regulasi')->unsigned()->nullable();
            $table->string('reg_judul', 255);
            $table->string('reg_sinopsis', 255);
            $table->string('reg_dokumen', 100)->nullable();
            $table->enum('reg_tipe_dokumen', ['link', 'file'])->default('file');
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_regulasi_isDeleted');
            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('fk_t_kategori_regulasi', 't_regulasi_fk_m_kategori_regulasi_idx');

            $table->foreign('fk_t_kategori_regulasi', 'fk_t_regulasi_t_kategori_regulasi')
                ->references('kategori_reg_id')->on('t_kategori_regulasi')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_regulasi', function (Blueprint $table) {
            $table->dropForeign('fk_t_regulasi_t_kategori_regulasi');
        });

        Schema::dropIfExists('t_regulasi');
    }
};
