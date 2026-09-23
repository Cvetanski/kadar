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
        Schema::table('creator_profiles', function (Blueprint $table) {
            $table->unsignedInteger('daily_proposals_count')->default(0)->after('verified');
            $table->date('last_proposal_reset_date')->nullable()->after('daily_proposals_count');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('daily_invites_count')->default(0)->after('paddle_price_id');
            $table->date('last_invite_reset_date')->nullable()->after('daily_invites_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('creator_profiles', function (Blueprint $table) {
            $table->dropColumn(['daily_proposals_count', 'last_proposal_reset_date']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_invites_count', 'last_invite_reset_date']);
        });
    }
};
