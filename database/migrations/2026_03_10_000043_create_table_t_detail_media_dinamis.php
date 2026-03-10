<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_detail_media_dinamis', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('detail_media_dinamis_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_media_dinamis')->unsigned()->nullable();
            $table->enum('dm_type_media', ['file', 'link']);
            $table->string('dm_judul_media', 100)->nullable();
            $table->string('dm_media_upload', 200);
            $table->enum('status_media', ['aktif', 'nonaktif'])->default('aktif');
            $table->tinyInteger('isDeleted')->nullable()->default(0);
            $table->string('created_by', 30)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('isDeleted', 't_detail_media_dinamis_isDeleted');
            $table->index('fk_m_media_dinamis', 't_detail_media_dinamis_fk_m_media_dinamis_idx');

            $table->foreign('fk_m_media_dinamis', 't_detail_media_dinamis_fk_m_media_dinamis')
                ->references('media_dinamis_id')->on('m_media_dinamis')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_detail_media_dinamis', function (Blueprint $table) {
            $table->dropForeign('t_detail_media_dinamis_fk_m_media_dinamis');
        });

        Schema::dropIfExists('t_detail_media_dinamis');
    }
};
