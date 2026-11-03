<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_supported_locale_is_selected_for_the_request_and_shared_to_inertia(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->withSession(['locale' => 'es'])
            ->get(route('dashboard'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('locale', 'es')
                ->where('translations.shell.dashboard', 'Panel'),
            );
    }

    public function test_unsupported_locale_falls_back_to_the_configured_locale(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->withSession(['locale' => 'xx'])
            ->get(route('dashboard'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('locale', config('app.fallback_locale')),
            );
    }

    public function test_authenticated_users_can_change_locale(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('locale.update'), ['locale' => 'es'])
            ->assertRedirect()
            ->assertSessionHas('locale', 'es');
    }
}
