<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_upload_pengumuman', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('upload_pengumuman_id')->unsigned()->autoIncrement();
            $table->integer('fk_t_pengumuman')->unsigned()->nullable();
            $table->string('up_thumbnail', 100)->nullable();
            $table->enum('up_type', ['link', 'file', 'konten']);
            $table->string('up_value', 100)->nullable();
            $table->longText('up_konten')->nullable();
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('isDeleted', 't_upload_pengumuman_isDeleted');
            $table->index('fk_t_pengumuman', 't_upload_pengumuman_fk_m_pengumuman_idx');

            $table->foreign('fk_t_pengumuman', 't_upload_pengumuman_fk_t_pengumuman')
                ->references('pengumuman_id')->on('t_pengumuman')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_upload_pengumuman', function (Blueprint $table) {
            $table->dropForeign('t_upload_pengumuman_fk_t_pengumuman');
        });

        Schema::dropIfExists('t_upload_pengumuman');
    }
};
