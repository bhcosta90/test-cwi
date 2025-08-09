<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

final class ExternalController extends Controller
{
    public function __invoke()
    {
        $response = Http::get(config('app.ms_posts') . '/posts');

        if (Response::HTTP_OK !== $response->status()) {
            return response()->json(['message' => 'External service is unreachable'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json($response->json(), Response::HTTP_OK);
    }
}
