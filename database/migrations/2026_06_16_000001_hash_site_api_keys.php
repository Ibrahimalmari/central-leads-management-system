<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('api_key_preview')->nullable()->after('api_key');
        });

        DB::table('sites')
            ->select(['id', 'api_key'])
            ->orderBy('id')
            ->get()
            ->each(function (object $site): void {
                $apiKey = (string) $site->api_key;

                if ($apiKey === '' || preg_match('/^[a-f0-9]{64}$/i', $apiKey) === 1) {
                    return;
                }

                DB::table('sites')
                    ->where('id', $site->id)
                    ->update([
                        'api_key' => hash('sha256', $apiKey),
                        'api_key_preview' => substr($apiKey, 0, 8).'...'.substr($apiKey, -4),
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('api_key_preview');
        });
    }
};
