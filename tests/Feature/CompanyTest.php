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
