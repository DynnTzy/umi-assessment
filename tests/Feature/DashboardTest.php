<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        $this->assertSame(
            'klinik',
            $user->fresh()->role,
            'User factory seharusnya menghasilkan role klinik.'
        );

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        dump([
            'role' => $user->fresh()->role,
            'clinic_id' => $user->fresh()->clinic_id,
            'status' => $response->status(),
            'exception' => $response->exception?->getMessage(),
        ]);

        $response->assertOk();
    }
}
