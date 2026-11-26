<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController
{
    public function store(Request $request, Lugar $lugar): RedirectResponse
    {
        $attributes = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:500'],
        ]);

        $review = Review::query()->firstOrCreate([
            'lugar_id' => $lugar->getKey(),
            'user_id' => $request->user()->getKey(),
        ], $attributes);

        $detail = redirect()->route('lugares.show', $lugar);

        if (! $review->wasRecentlyCreated) {
            return $detail->withErrors(['review' => 'Ya publicaste una reseña para este lugar.']);
        }

        return $detail->with('status', 'Tu reseña se publicó.');
    }

    public function vote(Request $request, Review $review): RedirectResponse
    {
        $viewerId = $request->user()->getKey();
        $detail = redirect()->route('lugares.show', $review->lugar()->firstOrFail());

        if ($review->user_id === $viewerId) {
            return $detail->withErrors(['vote' => 'No podés votar tu propia reseña.']);
        }

        return DB::transaction(function () use ($review, $viewerId, $detail): RedirectResponse {
            $lockedReview = Review::query()->lockForUpdate()->findOrFail($review->getKey());
            $vote = $lockedReview->votes()->firstOrCreate(['user_id' => $viewerId]);

            if (! $vote->wasRecentlyCreated) {
                return $detail->withErrors(['vote' => 'Ya votaste esta reseña.']);
            }

            if ($lockedReview->rewarded_at === null && $lockedReview->votes()->count() >= 2) {
                $rewarded = Review::query()->whereKey($lockedReview->getKey())
                    ->whereNull('rewarded_at')->update(['rewarded_at' => now()]);

                if ($rewarded === 1) {
                    User::query()->whereKey($lockedReview->user_id)->increment('points', 100);
                }
            }

            return $detail->with('status', 'Marcaste esta reseña como útil.');
        }, attempts: 3);
    }
}
