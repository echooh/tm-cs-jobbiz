<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

pest()->use(RefreshDatabase::class);

test('register creates a user and returns a token', function () {
    $payload = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $this->postJson('/api/register', $payload)
        ->assertCreated()
        ->assertJsonPath('data.email', 'jane@example.com')
        ->assertJsonStructure(['data' => ['id', 'name', 'email'], 'token']);

    $user = User::firstWhere('email', 'jane@example.com');
    expect(Hash::check('password123', $user->password))->toBeTrue();
});
