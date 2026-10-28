<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

pest()->use(RefreshDatabase::class);

test('a new user can be created', function () {
    Sanctum::actingAs(User::factory()->create());

    $payload = User::factory()->raw([
        'password' => '123test456',
        'password_confirmation' => '123test456',
    ]);

    $response = $this->postJson('/api/users', $payload);
    $response->assertCreated();
    $this->assertDatabaseHas('users', ['email' => $payload['email']]);
});

test('get /api/users shows a list of users', function () {
    $this->withoutExceptionHandling();
    $users = User::factory()->count(5)->create();
    Sanctum::actingAs($users->first());
    
    $this->getJson('/api/users')
        ->assertOk()
        ->assertJsonCount(5, 'data');    
});
