<?php

declare(strict_types = 1);

use App\Models\User;

it('lists users ordered by name and supports per_page parameter', function (): void {
    $users = User::factory()->count(3)->create([
    ]);
    $users[0]->update(['name' => 'Charlie']);
    $users[1]->update(['name' => 'Alice']);
    $users[2]->update(['name' => 'Bob']);

    $response = $this->getJson(route('user.index', ['per_page' => 2]));

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                ['id', 'name', 'email', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ]);

    $this->assertSame('Alice', $response->json('data.0.name'));
    $this->assertSame('Bob', $response->json('data.1.name'));
    $this->assertCount(2, $response->json('data'));
});

it('returns empty list when there are no users', function (): void {
    $response = $this->getJson(route('user.index'));

    $response->assertOk()
        ->assertJson([
            'data' => [],
        ]);
});

it('validates per_page must be integer between 1 and 100', function (): void {
    $this->getJson(route('user.index', ['per_page' => 0]))->assertUnprocessable();
    $this->getJson(route('user.index', ['per_page' => 101]))->assertUnprocessable();
    $this->getJson(route('user.index', ['per_page' => 'abc']))->assertUnprocessable();
});

it('shows a user by id', function (): void {
    $user = User::factory()->create();

    $response = $this->getJson(route('user.show', $user));

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
});

it('returns 404 when showing a non-existing user', function (): void {
    $response = $this->getJson(route('user.show', ['user' => -1]));

    $response->assertNotFound();
});

it('stores a user and returns the created resource', function (): void {
    $payload = [
        'name'                  => 'John Doe',
        'email'                 => 'john.doe@example.com',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $response = $this->postJson(route('user.store'), $payload);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'John Doe')
        ->assertJsonPath('data.email', 'john.doe@example.com');

    $this->assertDatabaseHas(User::class, [
        'email' => 'john.doe@example.com',
        'name'  => 'John Doe',
    ]);
});

it('validates required fields on store', function (): void {
    $response = $this->postJson(route('user.store'), []);
    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('validates unique email on store', function (): void {
    $existing = User::factory()->create(['email' => 'dupe@example.com']);

    $payload = [
        'name'                  => 'Jane',
        'email'                 => 'dupe@example.com',
        'password'              => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    $this->postJson(route('user.store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('requires password confirmation on store', function (): void {
    $payload = [
        'name'     => 'Jane',
        'email'    => 'jane@example.com',
        'password' => 'Password123!',
    ];

    $this->postJson(route('user.store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('updates a user basic fields', function (): void {
    $user = User::factory()->create([
        'name'  => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $payload = [
        'name'  => 'New Name',
        'email' => 'old@example.com',
    ];

    $response = $this->putJson(route('user.update', $user), $payload);

    $response->assertOk()
        ->assertJsonPath('data.name', 'New Name');

    $this->assertDatabaseHas(User::class, [
        'id'   => $user->id,
        'name' => 'New Name',
    ]);
});

it('updates a user password when provided with confirmation', function (): void {
    $user = User::factory()->create([
        'email' => 'secure@example.com',
    ]);

    $payload = [
        'name'                  => $user->name,
        'email'                 => 'secure@example.com',
        'password'              => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ];

    $this->putJson(route('user.update', $user), $payload)
        ->assertOk();
});

it('validates unique email on update', function (): void {
    $a = User::factory()->create(['email' => 'a@example.com']);
    $b = User::factory()->create(['email' => 'b@example.com']);

    $payload = [
        'name' => $b->name,
    ];

    $this->putJson(route('user.update', $b), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('requires name and valid email on update', function (): void {
    $user = User::factory()->create();

    $payload = [
        'email' => 'not-an-email',
    ];

    $this->putJson(route('user.update', $user), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email']);
});

it('requires password confirmation when password provided on update', function (): void {
    $user = User::factory()->create();

    $payload = [
        'name'     => $user->name,
        'email'    => $user->email,
        'password' => 'AnotherPassword123!',
    ];

    $this->putJson(route('user.update', $user), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('returns 404 when updating a non-existing user', function (): void {
    $payload = [
        'name'                  => 'Any',
        'email'                 => 'any@example.com',
        'password'              => null,
        'password_confirmation' => null,
    ];

    $this->putJson(route('user.update', ['user' => -1]), $payload)
        ->assertNotFound();
});

it('soft deletes a user', function (): void {
    $user = User::factory()->create();

    $this->deleteJson(route('user.destroy', $user))
        ->assertNoContent();

    $this->assertSoftDeleted('users', ['id' => $user->id]);
});

it('returns 404 when deleting a non-existing user', function (): void {
    $u = User::latest()->first();
    $this->deleteJson(route('user.destroy', ['user' => $u?->id + 1]))
        ->assertNotFound();
});

it('returns 405 when using unsupported method on resource item', function (): void {
    $user = User::factory()->create();
    $this->postJson(route('user.show', $user))
        ->assertStatus(405);
});
