<?php

declare(strict_types = 1);

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

it('returns 200 and proxies JSON from external service on success', function (): void {
    config(['app.ms_posts' => 'http://external.test']);

    $externalPayload = [
        ['id' => 1, 'title' => 'Post A'],
        ['id' => 2, 'title' => 'Post B'],
    ];

    Http::fake([
        'http://external.test/posts' => Http::response($externalPayload, Response::HTTP_OK, ['Content-Type' => 'application/json']),
    ]);

    $response = $this->getJson('/external');

    $response->assertOk()
        ->assertExactJson($externalPayload);

    // ensures the request was a simple GET to /posts (without leaking client query strings)
    Http::assertSent(fn ($request): bool => 'GET' === $request->method()
        && 'http://external.test/posts' === $request->url()
    );
});

it('returns 503 when external service responds with non-200 status', function (int $status): void {
    config(['app.ms_posts' => 'http://external.test']);

    Http::fake([
        'http://external.test/posts' => Http::response(['error' => 'ups'], $status),
    ]);

    $response = $this->getJson('/external');

    $response->assertStatus(Response::HTTP_SERVICE_UNAVAILABLE)
        ->assertJson(['message' => 'External service is unreachable']);
})->with([404, 500, 301, 403]);

it('supports HEAD requests (mirrors GET route)', function (): void {
    config(['app.ms_posts' => 'http://external.test']);

    Http::fake([
        'http://external.test/posts' => Http::response([['id' => 1]], Response::HTTP_OK),
    ]);

    $response = $this->call('HEAD', '/external');

    $response->assertOk();
});

it('returns 405 for unsupported HTTP methods', function (string $method): void {
    $response = match ($method) {
        'POST'   => $this->postJson('/external'),
        'PUT'    => $this->putJson('/external'),
        'PATCH'  => $this->patchJson('/external'),
        'DELETE' => $this->deleteJson('/external'),
        default  => throw new InvalidArgumentException('Unsupported test method'),
    };

    $response->assertStatus(405);
})->with(['POST', 'PUT', 'PATCH', 'DELETE']);
