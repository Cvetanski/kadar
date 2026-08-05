<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['project_id', 'category_id']);
        });

        $projects = DB::table('projects')->whereNotNull('category_id')->get(['id', 'category_id']);

        foreach ($projects as $project) {
            DB::table('project_category')->insert([
                'project_id' => $project->id,
                'category_id' => $project->category_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
        });

        $pivots = DB::table('project_category')->orderBy('project_id')->get(['project_id', 'category_id']);

        foreach ($pivots->unique('project_id') as $pivot) {
            DB::table('projects')->where('id', $pivot->project_id)->update(['category_id' => $pivot->category_id]);
        }

        Schema::dropIfExists('project_category');
    }
};
