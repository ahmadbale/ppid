<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_user', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('user_id')->unsigned()->autoIncrement();
            $table->string('password', 100);
            $table->string('nama_pengguna', 255);
            $table->string('alamat_pengguna', 255);
            $table->string('no_hp_pengguna', 20);
            $table->string('email_pengguna', 100);
            $table->string('pekerjaan_pengguna', 50);
            $table->string('nik_pengguna', 20);
            $table->string('upload_nik_pengguna', 100)->nullable();
            $table->string('foto_profil', 100)->nullable();
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->unique('email_pengguna', 'email_UNIQUE');
            $table->unique('nik_pengguna', 'nik_pengguna_UNIQUE');
            $table->unique('no_hp_pengguna', 'no_hp_pengguna_UNIQUE');
            $table->index('isDeleted', 'm_user_isDeleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_user');
    }
};
