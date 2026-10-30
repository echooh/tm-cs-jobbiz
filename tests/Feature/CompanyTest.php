<?php

use App\Models\Company;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('index shows paginated companies', function () {
    Sanctum::actingAs(User::factory()->create());

    $companies = Company::factory()->count(5)->create();

    $request = $this->getJson('/api/companies');

    $request->assertOk();
});

test('store creates new company', function () {
    Sanctum::actingAs(User::factory()->create());

    $payload = [
        'name' => 'Test Company',
        'description' => 'This is a test company',
        'website' => 'https://www.test-company.de',
    ];

    $request = $this->postJson('/api/companies', $payload);
    $request->assertCreated();
    
    $this->assertDatabaseHas('companies', ['website' => $payload['website']]);
});

test('show returns the correct company', function () {
    Sanctum::actingAs(User::factory()->create());

    $companies = Company::factory()->count(5)->create();

    $thirdCompany = $companies[2];

    $this->getJson("/api/companies/{$thirdCompany->id}")
        ->assertOk();
});
