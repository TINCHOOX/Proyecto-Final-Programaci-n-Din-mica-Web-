<?php

namespace App\Http\Controllers;

use App\Models\Dinosaur;
use App\Models\UserPresence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PresenceController
{
    public function index(Request $request): JsonResponse
    {
        return $this->nearby($request->user()->getKey());
    }

    public function update(Request $request): JsonResponse
    {
        $position = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $userId = $request->user()->getKey();
        $now = now();

        UserPresence::query()->where('expires_at', '<=', $now)->delete();
        UserPresence::query()->upsert([[
            'user_id' => $userId,
            'latitude' => round((float) $position['latitude'], 3),
            'longitude' => round((float) $position['longitude'], 3),
            'expires_at' => $now->addMinutes(3),
        ]], ['user_id'], ['latitude', 'longitude', 'expires_at']);

        return $this->nearby($userId);
    }

    public function destroy(Request $request): Response
    {
        UserPresence::query()->whereKey($request->user()->getKey())->delete();

        return response()->noContent(204, ['Cache-Control' => 'private, no-store']);
    }

    private function nearby(int $userId): JsonResponse
    {
        $active = UserPresence::query()->where('expires_at', '>', now());
        $viewer = (clone $active)->find($userId);

        if ($viewer === null) {
            return response()->json(['data' => [], 'sharing' => false], 200, ['Cache-Control' => 'private, no-store']);
        }

        $latitudeRange = 8 / 111;
        $longitudeRange = $latitudeRange / max(0.01, cos(deg2rad($viewer->latitude)));
        $neighbors = $active->whereKeyNot($userId)
            ->whereBetween('latitude', [$viewer->latitude - $latitudeRange, $viewer->latitude + $latitudeRange])
            ->whereBetween('longitude', [$viewer->longitude - $longitudeRange, $viewer->longitude + $longitudeRange])
            ->with('user:id,equipped_dinosaur_id', 'user.equippedDinosaur:id,image_path')
            ->latest('expires_at')->orderBy('user_id')->limit(30)->get();

        $starter = Dinosaur::query()->where('is_starter', true)->orderBy('sort_order')->value('image_path');
        $data = $neighbors->map(fn (UserPresence $neighbor) => [
            'latitude' => $neighbor->latitude,
            'longitude' => $neighbor->longitude,
            'avatar' => $this->avatar($neighbor->user?->equippedDinosaur?->image_path)
                ?? $this->avatar($starter)
                ?? asset('images/dinomaps/dino-1.webp'),
        ]);

        return response()->json(['data' => $data, 'sharing' => true], 200, ['Cache-Control' => 'private, no-store']);
    }

    private function avatar(?string $path): ?string
    {
        return $path !== null && preg_match('~^images/dinomaps/[a-z0-9-]+\.webp$~D', $path) === 1
            ? asset($path)
            : null;
    }
}
