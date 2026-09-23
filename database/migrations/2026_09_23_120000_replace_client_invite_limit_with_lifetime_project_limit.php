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
        Schema::table('users', function (Blueprint $table) {
            // Superseded: the free-client limit is a lifetime project count,
            // not a daily invite count.
            $table->dropColumn(['daily_invites_count', 'last_invite_reset_date']);

            $table->boolean('lifetime_project_used')->default(false)->after('plan_prompt_dismissed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('lifetime_project_used');
            $table->unsignedInteger('daily_invites_count')->default(0);
            $table->date('last_invite_reset_date')->nullable();
        });
    }
};
