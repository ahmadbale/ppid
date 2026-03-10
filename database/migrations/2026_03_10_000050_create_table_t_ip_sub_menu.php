<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ip_sub_menu', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('ip_sub_menu_id')->unsigned()->autoIncrement();
            $table->integer('fk_t_ip_sub_menu_utama')->unsigned()->nullable();
            $table->string('nama_ip_sm', 100);
            $table->string('dokumen_ip_sm', 100);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_ip_sub_menu_isDeleted');
            $table->index('fk_t_ip_sub_menu_utama', 'fk_t_ip_sub_menu_t_ip_sub_menu_utama1_idx');

            $table->foreign('fk_t_ip_sub_menu_utama', 't_ip_sub_menu_fk_t_ip_sub_menu_utama')
                ->references('ip_sub_menu_utama_id')->on('t_ip_sub_menu_utama')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_ip_sub_menu', function (Blueprint $table) {
            $table->dropForeign('t_ip_sub_menu_fk_t_ip_sub_menu_utama');
        });

        Schema::dropIfExists('t_ip_sub_menu');
    }
};
