<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_konten', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('web_konten_id')->unsigned()->autoIncrement();
            $table->integer('fk_web_menu')->unsigned()->nullable();
            $table->string('wk_judul_konten', 200);
            $table->longText('wk_deskripsi_konten')->nullable();
            $table->enum('wk_status_konten', ['aktif', 'nonaktif'])->default('aktif');
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 'web_konten_isDeleted');
            $table->index('fk_web_menu', 'web_konten_fk_web_menu_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_konten');
    }
};
