<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_form_pk_diri_sendiri', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('form_pk_diri_sendiri_id')->unsigned()->autoIncrement();
            $table->string('pk_nama_pengguna', 255);
            $table->string('pk_alamat_pengguna', 255);
            $table->string('pk_pekerjaan_pengguna', 100);
            $table->string('pk_no_hp_pengguna', 20);
            $table->string('pk_email_pengguna', 100);
            $table->string('pk_upload_nik_pengguna', 100);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_form_pk_diri_sendiri_isDeleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_form_pk_diri_sendiri');
    }
};
