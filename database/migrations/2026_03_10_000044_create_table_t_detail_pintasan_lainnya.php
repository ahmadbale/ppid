<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_detail_pintasan_lainnya', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('detail_pintasan_lainnya_id')->unsigned()->autoIncrement();
            $table->integer('fk_pintasan_lainnya')->unsigned()->nullable();
            $table->string('dpl_judul', 100);
            $table->string('dpl_url', 100);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_detail_pintasan_lainnya_isDeleted');
            $table->index('fk_pintasan_lainnya', 't_detail_pintasan_lainnya_fk_t_pintasan_lainnya_idx');

            $table->foreign('fk_pintasan_lainnya', 'fk_detail_pintasan_lainnya_t_pintasan_lainnya')
                ->references('pintasan_lainnya_id')->on('t_pintasan_lainnya')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_detail_pintasan_lainnya', function (Blueprint $table) {
            $table->dropForeign('fk_detail_pintasan_lainnya_t_pintasan_lainnya');
        });

        Schema::dropIfExists('t_detail_pintasan_lainnya');
    }
};
