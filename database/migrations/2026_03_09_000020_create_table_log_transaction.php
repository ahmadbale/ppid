<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_transaction', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('log_transaction_id')->unsigned()->autoIncrement();
            $table->enum('log_transaction_jenis', ['CREATED', 'UPDATED', 'DELETED', 'APPROVED', 'REJECTED'])->default('CREATED');
            $table->integer('log_transaction_aktivitas_id')->nullable();
            $table->string('log_transaction_aktivitas', 255);
            $table->string('log_transaction_level', 50);
            $table->string('log_transaction_pelaku', 30);
            $table->timestamp('log_transaction_tanggal_aktivitas')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_transaction');
    }
};
