<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

final class ExternalController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $urlBase = is_string(config('app.ms_posts')) ? config('app.ms_posts') : '';

        if (blank($urlBase)) {
            return response()->json(['message' => 'External service URL is not configured'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $response = Http::get($urlBase . '/posts');

        if (Response::HTTP_OK !== $response->status()) {
            return response()->json(['message' => 'External service is unreachable'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json($response->json(), Response::HTTP_OK);
    }
}
