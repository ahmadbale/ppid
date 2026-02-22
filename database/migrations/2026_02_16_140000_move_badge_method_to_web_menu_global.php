<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('web_menu_url', 'badge_method')) {
            Schema::table('web_menu_url', function (Blueprint $table) {
                $table->dropColumn('badge_method');
            });
        }

        if (!Schema::hasColumn('web_menu_global', 'wmg_badge_method')) {
            Schema::table('web_menu_global', function (Blueprint $table) {
                $table->string('wmg_badge_method', 100)->nullable()->after('wmg_nama_default')
                    ->comment('Method name in controller to get badge count (e.g., getBadgeCount)');
            });
        }
    }

    public function down(): void
    {
        Schema::table('web_menu_global', function (Blueprint $table) {
            $table->dropColumn('wmg_badge_method');
        });

        Schema::table('web_menu_url', function (Blueprint $table) {
            $table->string('badge_method', 100)->nullable()->after('controller_name')
                ->comment('Method name in controller to get badge count (e.g., getBadgeCount)');
        });
    }
};
