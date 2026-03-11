<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_konten_images', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('konten_images_id')->unsigned()->autoIncrement();
            $table->integer('fk_web_konten')->unsigned()->nullable();
            $table->string('wki_image_webkonten', 100);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 'web_konten_images_isDeleted');
            $table->index('fk_web_konten', 'web_konten_images_fk_web_konten_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_konten_images');
    }
};
