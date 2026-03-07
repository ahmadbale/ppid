<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('set_user_hak_akses', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('set_user_hak_akses_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_hak_akses')->unsigned();
            $table->integer('fk_m_user')->unsigned();
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('fk_m_user', 'fk_set_level_user_m_user1_idx');
            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('fk_m_hak_akses', 'fk_set_level_user_m_level1_idx');
            $table->index('isDeleted', 'set_user_hak_akses_isDeleted');

            $table->foreign('fk_m_user', 'fk_set_user_hak_akses_m_user1')
                ->references('user_id')->on('m_user')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');

            $table->foreign('fk_m_hak_akses', 'fk_set_user_hak_aksesr_m_hak_akses1')
                ->references('hak_akses_id')->on('m_hak_akses')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('set_user_hak_akses', function (Blueprint $table) {
            $table->dropForeign('fk_set_user_hak_akses_m_user1');
            $table->dropForeign('fk_set_user_hak_aksesr_m_hak_akses1');
        });

        Schema::dropIfExists('set_user_hak_akses');
    }
};
