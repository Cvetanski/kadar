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
            $table->boolean('is_admin')->default(false)->after('role');
        });

        // Admin access used to be tied to role='admin', which meant an admin
        // account couldn't also act as a client or creator. Existing admin
        // accounts get the is_admin flag and fall back to the 'client' role
        // so they keep working; role is no longer the source of truth for
        // admin access.
        DB::table('users')->where('role', 'admin')->update([
            'is_admin' => true,
            'role' => 'client',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('is_admin', true)->update(['role' => 'admin']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
