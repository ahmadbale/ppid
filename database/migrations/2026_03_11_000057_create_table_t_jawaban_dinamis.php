<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_jawaban_dinamis', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('jawaban_dinamis_id')->unsigned()->autoIncrement();
            $table->integer('fk_t_form_dinamis')->unsigned()->nullable();
            $table->integer('fk_t_pertanyaan_dinamis')->unsigned()->nullable();
            $table->string('jd_jawaban', 255);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('fk_t_form_dinamis', 't_jawaban_dinamis_fk_t_form_dinamis_idx');
            $table->index('fk_t_pertanyaan_dinamis', 't_jawaban_dinamis_fk_t_pertanyaan_dinamis_idx');
            $table->index('isDeleted', 't_jawaban_dinamis_isDeleted');

            $table->foreign('fk_t_form_dinamis', 't_jawaban_dinamis_fk_t_form_dinamis')
                ->references('form_dinamis_id')->on('t_form_dinamis')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');

            $table->foreign('fk_t_pertanyaan_dinamis', 't_jawaban_dinamis_fk_t_pertanyaan_dinamis')
                ->references('pertanyaan_dinamis_id')->on('t_pertanyaan_dinamis')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_jawaban_dinamis', function (Blueprint $table) {
            $table->dropForeign('t_jawaban_dinamis_fk_t_form_dinamis');
            $table->dropForeign('t_jawaban_dinamis_fk_t_pertanyaan_dinamis');
        });

        Schema::dropIfExists('t_jawaban_dinamis');
    }
};
