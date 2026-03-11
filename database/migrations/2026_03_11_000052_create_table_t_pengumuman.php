<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_pengumuman', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('pengumuman_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_pengumuman_dinamis')->unsigned()->nullable();
            $table->string('peg_judul', 200)->nullable();
            $table->string('peg_slug', 100)->nullable();
            $table->enum('status_pengumuman', ['aktif', 'tidak aktif'])->default('aktif');
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->unique('peg_slug', 'peg_slug');
            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('isDeleted', 't_pengumuman_isDeleted');
            $table->index('fk_m_pengumuman_dinamis', 'fk_t_pengumuman_m_pengumuman_dinamis1_idx');

            $table->foreign('fk_m_pengumuman_dinamis', 'fk_t_pengumuman_m_pengumuman_dinamis1')
                ->references('pengumuman_dinamis_id')->on('m_pengumuman_dinamis')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_pengumuman', function (Blueprint $table) {
            $table->dropForeign('fk_t_pengumuman_m_pengumuman_dinamis1');
        });

        Schema::dropIfExists('t_pengumuman');
    }
};
