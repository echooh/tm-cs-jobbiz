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

test('update changes property values of company', function () {
    Sanctum::actingAs(User::factory()->create());

    $company = Company::factory()->create();

    $payload = [
        'name' => 'different name',
        'description' => 'one hellova description',
        'website' => 'https://www.new-website.com',
    ];

    $this->putJson("/api/companies/{$company->id}", $payload)
        ->assertOk();

    $this->assertDatabaseHas('companies',[
        'id' => $company->id,
        'website' => 'https://www.new-website.com',
    ]);
});

test('update doesnt take a website thats already in use by another company', function () {
    Sanctum::actingAs(User::factory()->create());

    $company = Company::factory()->create();
    $company2 = Company::factory()->create(['website' => 'https://www.in-use.com']);

    $this->putJson("/api/companies/{$company->id}", ['website' => 'https://www.in-use.com'])
        ->assertInvalid('website');
});

test('delete removes company entry from database', function () {
    Sanctum::actingAs(User::factory()->create());

    $company = Company::factory()->create();

    $this->deleteJson("/api/companies/{$company->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('companies', ['id' => $company->id]);
});
