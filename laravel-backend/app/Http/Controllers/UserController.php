<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class UserController extends Controller
{
    public function index(User $user, IndexRequest $request): AnonymousResourceCollection
    {
        $result = $user->query()
            ->orderBy('name')
            ->simplePaginate(perPage: $request->per_page);

        return UserResource::collection($result);
    }

    public function store(StoreRequest $request, User $user): UserResource
    {
        $userModel = $user->create($request->validated());

        return new UserResource($userModel);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UpdateRequest $request, User $user): UserResource
    {
        $user->update($request->validated());
        $user->save();

        return new UserResource($user);
    }

    public function destroy(User $user): Response
    {
        $user->delete();

        return response()->noContent();
    }
}
