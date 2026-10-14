<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('google_site_verification')->nullable()->after('site_keywords');
            $table->string('bing_site_verification')->nullable()->after('google_site_verification');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['google_site_verification', 'bing_site_verification']);
        });
    }
};
