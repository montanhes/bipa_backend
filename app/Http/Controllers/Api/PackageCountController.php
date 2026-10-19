<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackageCountRequest;
use Illuminate\Http\JsonResponse;

class PackageCountController extends Controller
{
    public function store(StorePackageCountRequest $request): JsonResponse
    {
        $user = $request->user();

        // client_uuid makes retries from the app's outbox idempotent.
        $count = $user->packageCounts()->firstOrCreate(
            ['client_uuid' => $request->validated('client_uuid')],
            ['quantity' => $request->validated('quantity'), 'counted_at' => $request->validated('counted_at')],
        );

        $user->forceFill(['last_seen_at' => now()])->save();

        return response()->json(
            $count->only(['id', 'quantity', 'client_uuid', 'counted_at']),
            $count->wasRecentlyCreated ? 201 : 200,
        );
    }
}
