<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->string('session_hash', 64)->nullable()->index()->after('ip');
            $table->text('referrer')->nullable()->after('url');
            $table->string('referrer_host')->nullable()->index()->after('referrer');
            $table->string('source', 100)->default('Tiesiogiai')->index()->after('referrer_host');
            $table->string('medium', 100)->nullable()->after('source');
            $table->string('campaign')->nullable()->after('medium');
            $table->string('term')->nullable()->after('campaign');
            $table->string('content')->nullable()->after('term');
            $table->string('device', 30)->nullable()->index()->after('content');
            $table->string('browser', 50)->nullable()->after('device');
            $table->boolean('is_entry')->default(false)->index()->after('browser');
        });

    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex(['session_hash']);
            $table->dropIndex(['referrer_host']);
            $table->dropIndex(['source']);
            $table->dropIndex(['device']);
            $table->dropColumn([
                'session_hash', 'referrer', 'referrer_host', 'source', 'medium',
                'campaign', 'term', 'content', 'device', 'browser', 'is_entry',
            ]);
        });
    }
};
