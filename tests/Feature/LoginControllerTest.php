<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_successfully()
    {
        $user = User::factory()->create([
            'password' => bcrypt('123456')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => '123456'
        ]);

        $response->assertJsonStructure([
            'token'
        ]);
    }

    public function test_login_fails()
    {
        $user = User::factory()->create([
            'password' => bcrypt('123456')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => '123'
        ]);

        $response->assertJsonValidationErrors('email');
    }

    public function test_logout()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->postJson('/api/logout');

        $response->assertNoContent();;
    }

    public function test_retrive_current_user()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->getJson('/api/user');

        $response->assertOk();
    }
}
