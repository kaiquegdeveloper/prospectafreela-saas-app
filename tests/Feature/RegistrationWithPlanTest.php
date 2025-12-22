<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationWithPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_new_plan_name(): void
    {
        $response = $this->post('/register', [
            'name' => 'Cliente Novo',
            'email' => 'cliente@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'plan_name' => 'Plano Premium',
        ]);

        $this->assertAuthenticated();

        $plan = Plan::first();

        $this->assertNotNull($plan);
        $this->assertEquals('Plano Premium', $plan->name);
        $this->assertDatabaseHas('users', [
            'email' => 'cliente@example.com',
            'plan_id' => $plan->id,
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
    }
}

