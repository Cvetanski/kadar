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
        Schema::create('skill_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['skill_id', 'category_id']);
        });

        // Backfill the pivot from the existing single-category column so no
        // skill/category association is lost by the switch to many-to-many.
        $now = now();
        $rows = DB::table('skills')->whereNotNull('category_id')->get(['id', 'category_id']);

        foreach ($rows as $row) {
            DB::table('skill_category')->insert([
                'skill_id' => $row->id,
                'category_id' => $row->category_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Schema::table('skills', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Best-effort restore: take one category per skill from the pivot.
        $pivotRows = DB::table('skill_category')->orderBy('id')->get(['skill_id', 'category_id']);
        $seen = [];

        foreach ($pivotRows as $row) {
            if (isset($seen[$row->skill_id])) {
                continue;
            }

            $seen[$row->skill_id] = true;

            DB::table('skills')->where('id', $row->skill_id)->update(['category_id' => $row->category_id]);
        }

        Schema::dropIfExists('skill_category');
    }
};
