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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_legacy_free')->default(false)->after('is_admin');
            $table->timestamp('subscribed_until')->nullable()->after('is_legacy_free');
        });

        // Everyone who already has an account predates the subscription
        // requirement, so they're grandfathered in for free permanently.
        DB::table('users')->update(['is_legacy_free' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_legacy_free', 'subscribed_until']);
        });
    }
};
