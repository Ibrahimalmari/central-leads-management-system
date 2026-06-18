<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->boolean('notify_new_leads')->default(false)->after('logo_path');
            $table->json('notification_emails')->nullable()->after('notify_new_leads');
            $table->boolean('whatsapp_notifications_enabled')->default(false)->after('notification_emails');
            $table->string('whatsapp_webhook_url')->nullable()->after('whatsapp_notifications_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'notify_new_leads',
                'notification_emails',
                'whatsapp_notifications_enabled',
                'whatsapp_webhook_url',
            ]);
        });
    }
};
