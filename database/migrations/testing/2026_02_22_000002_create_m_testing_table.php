<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_testing', function (Blueprint $table) {
            $table->integer('m_testing_id')->autoIncrement();
            $table->integer('fk_m_testing_kategori');
            $table->string('testing_nama', 200);
            $table->text('testing_hasil')->nullable();
            
            $table->tinyInteger('isDeleted')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            
            $table->foreign('fk_m_testing_kategori')
                  ->references('m_testing_kategori_id')
                  ->on('m_testing_kategori')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_testing');
    }
};
