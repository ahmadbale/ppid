<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_timeline', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('timeline_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_kategori_form')->unsigned();
            $table->string('judul_timeline', 255);
            $table->string('timeline_file', 100)->nullable();
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 'm_timeline_isDeleted');
            $table->index('fk_m_kategori_form', 't_timeline_fk_m_kategori_form');

            $table->foreign('fk_m_kategori_form', 't_timeline_fk_m_kategori_form')
                ->references('kategori_form_id')->on('m_kategori_form')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_timeline', function (Blueprint $table) {
            $table->dropForeign('t_timeline_fk_m_kategori_form');
        });

        Schema::dropIfExists('t_timeline');
    }
};
