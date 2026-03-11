<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_upload_berita', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('upload_berita_id')->unsigned()->autoIncrement();
            $table->integer('fk_t_berita')->unsigned()->nullable();
            $table->enum('ub_type', ['link', 'upload']);
            $table->string('ub_value', 100);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('fk_t_berita', 'berita_id');
            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('isDeleted', 't_upload_berita_isDeleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_upload_berita');
    }
};
