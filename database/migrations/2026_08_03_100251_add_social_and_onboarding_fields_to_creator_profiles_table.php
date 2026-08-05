<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creator_profiles', function (Blueprint $table) {
            $table->string('instagram_url')->nullable()->after('equipment');
            $table->string('facebook_url')->nullable()->after('instagram_url');
            $table->string('website_url')->nullable()->after('facebook_url');
            $table->timestamp('onboarding_completed_at')->nullable()->after('website_url');
        });
    }

    public function down(): void
    {
        Schema::table('creator_profiles', function (Blueprint $table) {
            $table->dropColumn(['instagram_url', 'facebook_url', 'website_url', 'onboarding_completed_at']);
        });
    }
};
