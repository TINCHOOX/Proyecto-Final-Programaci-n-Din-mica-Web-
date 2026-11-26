<?php

namespace App\Http\Controllers;

use App\Models\TrafficReport;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TrafficReportController
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'types' => ['sometimes', 'array', 'max:5'],
            'types.*' => ['required', 'string', Rule::in(TrafficReport::TYPES)],
        ]);

        $viewer = $request->user();
        $query = TrafficReport::query()->where('expires_at', '>', now())->withCount('votes');

        if ($viewer) {
            $query->withExists(['votes as viewer_voted' => fn ($votes) => $votes->where('user_id', $viewer->getKey())]);
        }

        if (! empty($filters['types'])) {
            $query->whereIn('type', $filters['types']);
        }

        $reports = $query->latest()->latest('id')->limit(100)->get();

        return $this->respond($reports->map(fn (TrafficReport $report) => $this->reportData($report, $viewer))->all());
    }

    public function store(Request $request): JsonResponse
    {
        $endPointProhibited = $request->input('type') !== 'concurrencia';
        $attributes = $request->validate([
            'type' => ['required', 'string', Rule::in(TrafficReport::TYPES)],
            'description' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'end_latitude' => [Rule::prohibitedIf($endPointProhibited), 'required_with:end_longitude', 'nullable', 'numeric', 'between:-90,90'],
            'end_longitude' => [Rule::prohibitedIf($endPointProhibited), 'required_with:end_latitude', 'nullable', 'numeric', 'between:-180,180'],
        ]);

        $viewer = $request->user();
        $report = TrafficReport::create([
            ...$attributes,
            'user_id' => $viewer->getKey(),
            'expires_at' => now()->addHours(TrafficReport::LIFETIME_HOURS),
        ]);

        return $this->respond($this->reportData($report, $viewer), 201);
    }

    public function mine(Request $request): View
    {
        $reports = TrafficReport::query()->where('user_id', $request->user()->getKey())
            ->where('expires_at', '>', now())->withCount('votes')
            ->latest()->latest('id')->paginate(25);

        return view('transito.mis-reportes', ['reports' => $reports, 'labels' => TrafficReport::LABELS]);
    }

    public function destroy(Request $request, TrafficReport $report): Response|RedirectResponse
    {
        abort_unless($report->user_id === $request->user()->getKey(), 403);

        $report->delete();

        return $request->expectsJson()
            ? response()->noContent()
            : redirect()->route('reports.mine')->with('status', 'El aviso se eliminó.');
    }

    public function vote(Request $request, TrafficReport $report): JsonResponse
    {
        $viewer = $request->user();

        return DB::transaction(function () use ($report, $viewer): JsonResponse {
            $lockedReport = TrafficReport::query()->lockForUpdate()->findOrFail($report->getKey());
            abort_if($lockedReport->expires_at->lessThanOrEqualTo(now()), 410, 'El aviso ya venció.');
            abort_if($lockedReport->user_id === $viewer->getKey(), 422, 'No podés confirmar un aviso propio.');
            abort_if($lockedReport->votes()->where('user_id', $viewer->getKey())->exists(), 409, 'Ya confirmaste este aviso.');

            $lockedReport->votes()->create(['user_id' => $viewer->getKey()]);
            $lockedReport->votes_count = $lockedReport->votes()->count();
            $lockedReport->viewer_voted = true;
            $awardedPoints = 0;

            if ($lockedReport->votes_count >= 2 && $lockedReport->rewarded_at === null) {
                $rewarded = TrafficReport::query()->whereKey($lockedReport->getKey())
                    ->whereNull('rewarded_at')->update(['rewarded_at' => now()]);

                if ($rewarded === 1) {
                    $awardedPoints = 100;
                    User::query()->whereKey($lockedReport->user_id)->increment('points', $awardedPoints);
                }
            }

            return $this->respond($this->reportData($lockedReport, $viewer), extra: ['awarded_points' => $awardedPoints]);
        }, attempts: 3);
    }

    private function reportData(TrafficReport $report, ?User $viewer): array
    {
        return [
            'id' => $report->getKey(),
            'type' => $report->type,
            'description' => $report->description,
            'latitude' => $report->latitude,
            'longitude' => $report->longitude,
            'end_latitude' => $report->end_latitude,
            'end_longitude' => $report->end_longitude,
            'positive_votes' => (int) $report->votes_count,
            'created_at' => $report->created_at->toISOString(),
            'expires_at' => $report->expires_at->toISOString(),
            'can_vote' => $viewer !== null && $viewer->getKey() !== $report->user_id && ! $report->viewer_voted,
            'can_delete' => $viewer !== null && $viewer->getKey() === $report->user_id,
        ];
    }

    private function respond(array $data, int $status = 200, array $extra = []): JsonResponse
    {
        return response()->json(['data' => $data, ...$extra], $status, ['Cache-Control' => 'private, no-store']);
    }
}
