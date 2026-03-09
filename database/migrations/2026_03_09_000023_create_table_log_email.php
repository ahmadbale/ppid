<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_email', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('log_email_id')->unsigned()->autoIncrement();
            $table->enum('log_email_status', ['Disetujui', 'Ditolak']);
            $table->string('log_email_nama_pengirim', 100);
            $table->string('log_email_tujuan', 100);
            $table->timestamp('log_email_tanggal_dikirim')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_email');
    }
};
