<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Query builder avoids the new encrypted model cast while migrating legacy rows.
        DB::transaction(function (): void {
            DB::table('users')->whereNotNull('website_password')->orderBy('id')
                ->chunkById(100, function ($users): void {
                    foreach ($users as $user) {
                        try {
                            Crypt::decryptString($user->website_password);
                            continue; // Already encrypted (safe to retry).
                        } catch (\Illuminate\Contracts\Encryption\DecryptException) {
                            DB::table('users')->where('id', $user->id)->update([
                                'website_password' => Crypt::encryptString($user->website_password),
                            ]);
                        }
                    }
                });
        });
    }

    public function down(): void
    {
        // Never silently turn stored credentials back into plaintext on rollback.
        // Keep the encrypted cast when rolling back application code.
    }
};
