<?php

namespace Tests\Feature;

use App\Models\EntryType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EntryTest extends TestCase
{
    use WithFaker;

    //TODO: fazer teste de criação de entrada
    public function test_create_entry()
    {
        $this->assertTrue(true);

//        $response = $this->postJson('/entries', [
//            'value' => 10.0,
//            'type' => EntryType::EXPENSE,
//            ''
//        ]);
//
//        $response->assertStatus(201);
    }
}
