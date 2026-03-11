<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_kategori_regulasi', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('kategori_reg_id')->unsigned()->autoIncrement();
            $table->integer('fk_regulasi_dinamis')->unsigned()->nullable();
            $table->string('kr_kategori_reg_kode', 20);
            $table->string('kr_nama_kategori', 200);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->unique('kr_kategori_reg_kode', 'kategori_regulasi_kode_UNIQUE');
            $table->index('isDeleted', 't_kategori_regulasi_isDeleted');
            $table->index('fk_regulasi_dinamis', 't_kategori_regulasi_fk_m_regulasi_dinamis');

            $table->foreign('fk_regulasi_dinamis', 'fk_t_kategori_regulasi_m_regulasi_dinamis')
                ->references('regulasi_dinamis_id')->on('m_regulasi_dinamis')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_kategori_regulasi', function (Blueprint $table) {
            $table->dropForeign('fk_t_kategori_regulasi_m_regulasi_dinamis');
        });

        Schema::dropIfExists('t_kategori_regulasi');
    }
};
