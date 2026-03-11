<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_langkah_timeline', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('langkah_timeline_id')->unsigned()->autoIncrement();
            $table->integer('fk_t_timeline')->unsigned()->nullable();
            $table->string('langkah_timeline', 255);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('isDeleted', 't_langkah_timeline_isDeleted');
            $table->index('fk_t_timeline', 't_langkah_timeline_fk_m_timeline_idx');

            $table->foreign('fk_t_timeline', 't_timeline_fk_t_timeline')
                ->references('timeline_id')->on('t_timeline')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_langkah_timeline', function (Blueprint $table) {
            $table->dropForeign('t_timeline_fk_t_timeline');
        });

        Schema::dropIfExists('t_langkah_timeline');
    }
};
