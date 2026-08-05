<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('project_rate_from', 10, 2)->nullable();
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->boolean('remote_ok')->default(false);
            $table->boolean('verified')->default(false);
            $table->unsignedInteger('avg_response_hours')->nullable();
            $table->json('languages')->nullable();
            $table->json('equipment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_profiles');
    }
};
