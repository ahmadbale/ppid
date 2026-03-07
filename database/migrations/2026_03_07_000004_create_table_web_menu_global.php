<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_menu_global', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('web_menu_global_id')->unsigned()->autoIncrement();
            $table->integer('fk_web_menu_url')->unsigned()->nullable();
            $table->integer('wmg_parent_id')->unsigned()->nullable();
            $table->string('wmg_icon', 50)->nullable();
            $table->enum('wmg_type', ['general', 'special'])->default('general');
            $table->enum('wmg_kategori_menu', ['Menu Biasa', 'Group Menu', 'Sub Menu'])->default('Menu Biasa');
            $table->integer('wmg_urutan_menu')->default(0);
            $table->string('wmg_nama_default', 255);
            $table->string('wmg_badge_method', 100)->nullable();
            $table->enum('wmg_status_menu', ['aktif', 'nonaktif'])->default('aktif');
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('fk_web_menu_url', 'fk_web_menu_global_web_menu_url1_idx');
            $table->index('isDeleted', 'web_menu_global_isDeleted');
            $table->index('wmg_type', 'idx_wmg_type');

            $table->foreign('fk_web_menu_url', 'fk_web_menu_global_web_menu_url1')
                ->references('web_menu_url_id')->on('web_menu_url')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('web_menu_global', function (Blueprint $table) {
            $table->dropForeign('fk_web_menu_global_web_menu_url1');
        });

        Schema::dropIfExists('web_menu_global');
    }
};
