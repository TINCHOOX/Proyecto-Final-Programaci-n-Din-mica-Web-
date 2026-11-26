<?php

namespace App\Http\Controllers;

use App\Models\Dinosaur;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController
{
    public function index(Request $request): View
    {
        return view('shop.index', $this->catalog($request->user()));
    }

    public function collection(Request $request): View
    {
        $catalog = $this->catalog($request->user());
        $catalog['dinosaurs'] = $catalog['dinosaurs']
            ->whereIn('id', $catalog['ownedIds'])
            ->values();

        return view('shop.collection', $catalog);
    }

    public function redeem(Request $request, Dinosaur $dinosaur): RedirectResponse
    {
        $userId = $request->user()->getKey();
        if ($dinosaur->is_starter) {
            return back()->withErrors(['dinosaur' => 'Este dinosaurio ya está en tu colección.']);
        }

        $error = DB::transaction(function () use ($dinosaur, $userId): ?string {
            $user = User::query()->lockForUpdate()->findOrFail($userId);
            if ($user->dinosaurs()->whereKey($dinosaur->getKey())->exists()) {
                return 'Este dinosaurio ya está en tu colección.';
            }

            if ($user->points < $dinosaur->price) {
                return 'No tenés puntos suficientes para este canje.';
            }

            $user->decrement('points', $dinosaur->price);
            $user->dinosaurs()->attach($dinosaur->getKey());

            return null;
        }, attempts: 3);

        return $error === null
            ? back()->with('status', $dinosaur->name.' ya está en tu colección.')
            : back()->withErrors(['dinosaur' => $error]);
    }

    public function equip(Request $request, Dinosaur $dinosaur): RedirectResponse
    {
        $user = $request->user();

        if (! $dinosaur->is_starter && ! $user->dinosaurs()->whereKey($dinosaur->getKey())->exists()) {
            return back()->withErrors(['dinosaur' => 'Primero canjeá este dinosaurio para equiparlo.']);
        }

        $user->equipped_dinosaur_id = $dinosaur->getKey();
        $user->save();

        return back()->with('status', 'Equipaste a '.$dinosaur->name.'.');
    }

    private function catalog(User $user): array
    {
        $dinosaurs = Dinosaur::query()->orderBy('sort_order')->orderBy('id')->get();
        $starterId = $dinosaurs->firstWhere('is_starter', true)?->getKey();
        $ownedIds = $user->dinosaurs()
            ->pluck('dinosaurs.id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($starterId !== null) {
            $ownedIds[] = $starterId;
        }

        return [
            'dinosaurs' => $dinosaurs,
            'points' => (int) $user->points,
            'ownedIds' => $ownedIds,
            'equippedId' => $user->equipped_dinosaur_id === null
                ? $starterId
                : (int) $user->equipped_dinosaur_id,
        ];
    }
}
