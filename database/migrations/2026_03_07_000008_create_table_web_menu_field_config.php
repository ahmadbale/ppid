<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_menu_field_config', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('web_menu_field_config_id')->unsigned()->autoIncrement();
            $table->integer('fk_web_menu_url')->unsigned();
            $table->string('wmfc_column_name', 100);
            $table->string('wmfc_column_type', 100)->nullable();
            $table->string('wmfc_field_label', 255);
            $table->string('wmfc_field_type', 50);
            $table->json('wmfc_criteria')->nullable();
            $table->json('wmfc_validation')->nullable();
            $table->string('wmfc_fk_table', 100)->nullable();
            $table->string('wmfc_fk_pk_column', 100)->nullable();
            $table->json('wmfc_fk_display_columns')->nullable();
            $table->json('wmfc_fk_label_columns')->nullable();
            $table->string('wmfc_fk_priority_display', 100)->nullable();
            $table->integer('wmfc_max_length')->nullable();
            $table->string('wmfc_label_keterangan', 500)->nullable();
            $table->integer('wmfc_ukuran_max')->nullable();
            $table->tinyInteger('wmfc_display_list')->default(1);
            $table->integer('wmfc_order')->default(0);
            $table->tinyInteger('wmfc_is_primary_key')->default(0);
            $table->tinyInteger('wmfc_is_auto_increment')->default(0);
            $table->tinyInteger('wmfc_is_visible')->default(1);
            $table->tinyInteger('isDeleted')->default(0);
            $table->char('created_by', 36)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->char('updated_by', 36)->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->char('deleted_by', 36)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('fk_web_menu_url', 'idx_wmfc_menu_url');
            $table->index('wmfc_column_name', 'idx_wmfc_column_name');
            $table->index('wmfc_order', 'idx_wmfc_order');
            $table->index('wmfc_is_visible', 'idx_wmfc_visible');
            $table->index('isDeleted', 'idx_wmfc_isdeleted');

            $table->foreign('fk_web_menu_url', 'fk_wmfc_web_menu_url')
                ->references('web_menu_url_id')->on('web_menu_url')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('web_menu_field_config', function (Blueprint $table) {
            $table->dropForeign('fk_wmfc_web_menu_url');
        });

        Schema::dropIfExists('web_menu_field_config');
    }
};
