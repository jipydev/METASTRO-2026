<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InitialSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_is_redirected_to_initial_setup_after_login(): void
    {
        $user = User::factory()->create([
            'is_initial_setup_completed' => false,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('initial-setup.index'));
    }

    public function test_user_who_completed_setup_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'is_initial_setup_completed' => true,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_completed_user_cannot_revisit_initial_setup(): void
    {
        $user = User::factory()->create([
            'is_initial_setup_completed' => true,
        ]);

        $this->actingAs($user)
            ->get(route('initial-setup.index'))
            ->assertRedirect(route('dashboard'));
    }
}
