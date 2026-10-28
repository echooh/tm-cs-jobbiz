<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('a new user can be created', function () {
    $payload = User::factory()->raw([
        'password' => '123test456',
        'password_confirmation' => '123test456',
    ]);

    $response = $this->postJson('/api/users', $payload);
    $response->assertCreated();
    $this->assertDatabaseHas('users', ['email' => $payload['email']]);
});
