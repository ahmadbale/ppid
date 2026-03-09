<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_form_pi_orang_lain', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('form_pi_orang_lain_id')->unsigned()->autoIncrement();
            $table->string('pi_nama_pengguna_penginput', 255);
            $table->string('pi_alamat_pengguna_penginput', 255);
            $table->string('pi_no_hp_pengguna_penginput', 20);
            $table->string('pi_email_pengguna_penginput', 100);
            $table->string('pi_upload_nik_pengguna_penginput', 100);
            $table->string('pi_nama_pengguna_informasi', 255);
            $table->string('pi_alamat_pengguna_informasi', 255);
            $table->string('pi_no_hp_pengguna_informasi', 20);
            $table->string('pi_email_pengguna_informasi', 100);
            $table->string('pi_upload_nik_pengguna_informasi', 100);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_form_pi_orang_lain');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_form_pi_orang_lain');
    }
};
