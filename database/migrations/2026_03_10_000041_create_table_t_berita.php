<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_berita', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('berita_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_berita_dinamis')->unsigned()->nullable();
            $table->string('berita_judul', 150);
            $table->string('berita_slug', 150)->nullable();
            $table->string('berita_thumbnail', 100);
            $table->string('berita_thumbnail_deskripsi', 255);
            $table->longText('berita_deskripsi');
            $table->enum('status_berita', ['aktif', 'nonaktif'])->default('aktif');
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->unique('berita_slug', 'berita_slug');
            $table->index('fk_m_berita_dinamis', 'fk_m_berita_t_berita_dinamis1_idx');
            $table->index('isDeleted', 't_berita_isDeleted');

            $table->foreign('fk_m_berita_dinamis', 'fk_t_berita_m_berita_dinamis')
                ->references('berita_dinamis_id')->on('m_berita_dinamis')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_berita', function (Blueprint $table) {
            $table->dropForeign('fk_t_berita_m_berita_dinamis');
        });

        Schema::dropIfExists('t_berita');
    }
};
