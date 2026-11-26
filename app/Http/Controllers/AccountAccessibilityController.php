<?php

namespace App\Http\Controllers;

use App\Models\Dinosaur;
use App\Models\TrafficReport;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountAccessibilityController
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $reportCount = TrafficReport::query()->where('user_id', $user->getKey())->count();
        $equipped = $user->equippedDinosaur
            ?? Dinosaur::query()->where('is_starter', true)->first();

        return view('cuenta.index', compact('user', 'reportCount', 'equipped'));
    }

    public function showSettings(): View
    {
        return view('cuenta.configuracion');
    }

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $attributes = $request->validate([
            'color_mode' => ['required', 'string', Rule::in(array_keys(User::COLOR_MODES))],
        ], [
            'color_mode.required' => 'Elegí un modo de color.',
            'color_mode.string' => 'Elegí un modo de color válido.',
            'color_mode.in' => 'Elegí un modo de color válido.',
        ]);

        $user = $request->user();
        $user->color_mode = $attributes['color_mode'];
        $user->save();

        if ($request->expectsJson()) {
            return response()->json(['color_mode' => $user->color_mode]);
        }

        return back()->with('status', 'Guardamos tu modo de color.');
    }
}
