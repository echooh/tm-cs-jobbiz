<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;
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

test('/api/users/<id> shows a specific user', function() {
    $users = User::factory()->count(5)->sequence(
        fn (Sequence $s) => ['email' => 'user'.($s->index + 1).'@test.com']
    )->create();

    Sanctum::actingAs($users->first());

    $thirdUser = $users[2];

    $response = $this->getJson("/api/users/{$thirdUser->id}");
    $response->assertOk()
        ->assertJsonPath('data.id', $thirdUser->id)
        ->assertJsonPath('data.email', 'user3@test.com');

});

test('update user changes users property values', function () {
    $user = User::factory()->create([
        'name' => 'Peter Parker',
        'email' => 'pp@spooderman.com',
        'password' => 'peterparker2k',
    ]); 

    Sanctum::actingAs($user);

    $payload = [
        'name' => 'a unusual name',
        'email' => 'abc@test.com',
        'password' => 'prettysafe',
        'password_confirmation' => 'prettysafe',
    ];

    $response = $this->putJson("/api/users/{$user->id}", $payload);

    $response->assertOk();

    // check if its in the database too
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'abc@test.com'
    ]);
    // check passwords
    $this->assertTrue(Hash::check('prettysafe', $user->fresh()->password));
});

test('delete user removes user from database', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $this->deleteJson("/api/users/{$user->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('users', ['id' => $user->id]);

});
