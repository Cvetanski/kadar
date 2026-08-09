<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Country;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([CategorySeeder::class, CountrySeeder::class, CitySeeder::class]);
    }

    private function project(User $client): Project
    {
        $category = Category::first();
        $country = Country::first();

        $project = Project::create([
            'client_id' => $client->id,
            'title' => 'Original title',
            'description' => 'Original description',
            'country_id' => $country->id,
            'status' => 'open',
        ]);

        $project->categories()->attach($category);

        return $project;
    }

    /**
     * Regression test: same bug as the create form — the edit form's Alpine
     * x-data used to interpolate old('country_id', $project->country_id)
     * directly into the JS object literal. Submitting an update with the
     * country cleared (empty string) makes old('country_id', ...) return ''
     * instead of falling back, producing invalid JS ("countryId: ,") that
     * crashed Alpine for the whole edit form on re-render.
     */
    public function test_edit_form_survives_a_failed_submission_with_no_category_and_no_country(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $project = $this->project($client);

        $failedSubmit = $this->actingAs($client)->patch("/projects/{$project->id}", [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'category_ids' => [],
            'country_id' => '',
        ]);

        $failedSubmit->assertSessionHasErrors(['category_ids', 'country_id']);

        $response = $this->actingAs($client)->get("/projects/{$project->id}/edit");

        $response->assertOk();
        $response->assertDontSee('countryId: ,', false);
        $response->assertDontSee('cityId: ,', false);
        $response->assertDontSee('validation.required', false);
    }

    public function test_missing_category_shows_a_friendly_message_not_a_raw_validation_key(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $project = $this->project($client);

        $response = $this->actingAs($client)->patch("/projects/{$project->id}", [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'category_ids' => [],
            'country_id' => Country::first()->id,
        ]);

        $response->assertSessionHasErrors('category_ids');
        $this->assertSame(
            'Избери барем 1 категорија.',
            session('errors')->get('category_ids')[0]
        );
    }
}
