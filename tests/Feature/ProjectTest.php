<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Country;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([CategorySeeder::class, CountrySeeder::class, CitySeeder::class]);
    }

    public function test_client_can_create_a_project(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $category = Category::first();
        $country = Country::first();

        $response = $this->actingAs($client)->post('/projects', [
            'title' => 'Промотивно видео за ресторан',
            'description' => 'Ни треба кратко промотивно видео од 60 секунди.',
            'category_ids' => [$category->id],
            'budget_min' => 200,
            'budget_max' => 500,
            'deadline' => now()->addWeeks(2)->format('Y-m-d'),
            'country_id' => $country->id,
        ]);

        $this->assertDatabaseHas('projects', [
            'client_id' => $client->id,
            'title' => 'Промотивно видео за ресторан',
            'status' => 'open',
        ]);

        $project = $client->projects()->firstOrFail();
        $response->assertRedirect(route('projects.show', $project));
    }

    public function test_client_can_mark_budget_as_negotiable_instead_of_setting_amounts(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $category = Category::first();
        $country = Country::first();

        $response = $this->actingAs($client)->post('/projects', [
            'title' => 'Промотивно видео за ресторан',
            'description' => 'Ни треба кратко промотивно видео од 60 секунди.',
            'category_ids' => [$category->id],
            'budget_negotiable' => '1',
            'budget_min' => 200,
            'budget_max' => 500,
            'deadline' => now()->addWeeks(2)->format('Y-m-d'),
            'country_id' => $country->id,
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('projects', [
            'client_id' => $client->id,
            'title' => 'Промотивно видео за ресторан',
            'budget_min' => null,
            'budget_max' => null,
        ]);
    }

    public function test_creator_role_cannot_create_a_project(): void
    {
        $creator = User::factory()->create(['role' => 'creator']);
        $category = Category::first();

        $response = $this->actingAs($creator)->post('/projects', [
            'title' => 'Test',
            'description' => 'Test description',
            'category_ids' => [$category->id],
            'country_id' => Country::first()->id,
        ]);

        $response->assertForbidden();
    }

    public function test_budget_max_must_be_greater_than_or_equal_to_budget_min(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $category = Category::first();

        $response = $this->actingAs($client)->post('/projects', [
            'title' => 'Test project',
            'description' => 'Test description',
            'category_ids' => [$category->id],
            'budget_min' => 500,
            'budget_max' => 200,
            'country_id' => Country::first()->id,
        ]);

        $response->assertSessionHasErrors('budget_max');
    }

    public function test_category_must_exist(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->post('/projects', [
            'title' => 'Test project',
            'description' => 'Test description',
            'category_ids' => [9999],
            'country_id' => Country::first()->id,
        ]);

        $response->assertSessionHasErrors('category_ids.0');
    }

    public function test_country_is_required_unless_remote(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $category = Category::first();

        $response = $this->actingAs($client)->post('/projects', [
            'title' => 'Test project',
            'description' => 'Test description',
            'category_ids' => [$category->id],
        ]);

        $response->assertSessionHasErrors('country_id');

        $response = $this->actingAs($client)->post('/projects', [
            'title' => 'Test project',
            'description' => 'Test description',
            'category_ids' => [$category->id],
            'remote_ok' => '1',
        ]);

        $response->assertSessionDoesntHaveErrors('country_id');
    }

    public function test_my_projects_list_only_shows_own_projects(): void
    {
        $clientA = User::factory()->create(['role' => 'client']);
        $clientB = User::factory()->create(['role' => 'client']);
        $category = Category::first();
        $country = Country::first();

        $this->actingAs($clientA)->post('/projects', [
            'title' => 'Project A',
            'description' => 'Description A',
            'category_ids' => [$category->id],
            'country_id' => $country->id,
        ]);

        $this->actingAs($clientB)->post('/projects', [
            'title' => 'Project B',
            'description' => 'Description B',
            'category_ids' => [$category->id],
            'country_id' => $country->id,
        ]);

        $response = $this->actingAs($clientA)->get('/projects');

        $response->assertSee('Project A');
        $response->assertDontSee('Project B');
    }

    public function test_creator_cannot_view_my_projects_list(): void
    {
        $creator = User::factory()->create(['role' => 'creator']);

        $response = $this->actingAs($creator)->get('/projects');

        $response->assertForbidden();
    }

    public function test_missing_category_shows_a_friendly_message_not_a_raw_validation_key(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->post('/projects', [
            'title' => 'Test project',
            'description' => 'Test description',
        ]);

        $response->assertSessionHasErrors('category_ids');
        $this->assertSame(
            'Избери барем 1 категорија.',
            session('errors')->get('category_ids')[0]
        );
    }

    /**
     * Regression test: the create-project form's Alpine x-data block used to
     * interpolate old('country_id')/old('city_id') directly into the JS
     * object literal. A <select> always submits its field (even as an empty
     * string), so old('country_id', 'null') returned '' instead of the
     * fallback — producing invalid JS like "countryId: ," that crashed
     * Alpine for the entire form on every re-render after a failed submit
     * with no country selected. This is exactly the scenario a client hits
     * when they submit without picking a category AND without picking a
     * country in the same attempt.
     */
    public function test_create_form_survives_a_failed_submission_with_no_category_and_no_country(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $failedSubmit = $this->actingAs($client)->post('/projects', [
            'title' => 'Test project',
            'description' => 'Test description',
        ]);

        $failedSubmit->assertSessionHasErrors(['category_ids', 'country_id']);

        $response = $this->actingAs($client)->get('/projects/create');

        $response->assertOk();
        $response->assertDontSee('countryId: ,', false);
        $response->assertDontSee('cityId: ,', false);
        $response->assertDontSee('validation.required', false);
        $response->assertSee('countryId: null,', false);
        $response->assertSee('cityId: null,', false);
    }
}
