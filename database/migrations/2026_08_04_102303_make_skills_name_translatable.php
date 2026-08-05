<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->string('slug')->nullable()->unique()->after('id');
        });

        foreach (DB::table('skills')->get() as $skill) {
            DB::table('skills')->where('id', $skill->id)->update([
                'slug' => Str::slug($skill->name) ?: 'skill-'.$skill->id,
                // Re-encode the existing plain string as translatable JSON (mk only for
                // now) while the column is still varchar, so the type change below sees
                // valid JSON text rather than raw skill names.
                'name' => json_encode(['mk' => $skill->name]),
            ]);
        }

        // SQLite has no ALTER ... MODIFY and doesn't enforce column types anyway
        // (the JSON string already fits fine in the existing varchar/text storage),
        // so the native type change is MySQL-only.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE skills MODIFY name JSON NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE skills MODIFY name VARCHAR(255) NOT NULL');
        }

        foreach (DB::table('skills')->get() as $skill) {
            $name = json_decode($skill->name, true);
            DB::table('skills')->where('id', $skill->id)->update([
                'name' => $name['mk'] ?? array_values($name)[0] ?? $skill->name,
            ]);
        }

        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->unique('name');
        });
    }
};
