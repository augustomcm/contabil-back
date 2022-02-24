<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_retrive_current_user()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $response = $this->get('/api/user');

        $response->assertOk();
    }
}
