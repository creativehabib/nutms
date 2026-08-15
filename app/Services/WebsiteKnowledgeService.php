<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\Training;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class WebsiteKnowledgeService
{
    public function context(string $question): string
    {
        $terms = collect(preg_split('/[^\pL\pN]+/u', Str::lower($question)) ?: [])
            ->filter(fn (string $term): bool => mb_strlen($term) >= 3)
            ->unique()
            ->take(8)
            ->values();

        if ($terms->isEmpty()) {
            return '';
        }

        $colleges = College::query()
            ->where('is_active', true)
            ->where('approval_status', ApprovalStatus::Approved)
            ->where(function (Builder $query) use ($terms): void {
                foreach ($terms as $term) {
                    $query->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('college_code', 'like', "%{$term}%");
                }
            })
            ->with(['division:id,name,bn_name', 'district:id,name,bn_name', 'programs:id,college_id,level,name,items'])
            ->limit(5)
            ->get()
            ->map(function (College $college): string {
                $programs = $college->programs->map(function ($program): string {
                    $items = collect($program->items)->flatten()->filter(fn (mixed $item): bool => is_scalar($item) && filled($item))->implode(', ');

                    return trim("{$program->level} - {$program->name}".($items !== '' ? " ({$items})" : ''));
                })->implode('; ');

                return implode(' | ', array_filter([
                    "College: {$college->name}",
                    "code: {$college->college_code}",
                    'location: '.collect([$college->district?->name, $college->division?->name])->filter()->implode(', '),
                    $programs !== '' ? "programs/courses: {$programs}" : null,
                    'verified page: '.route('public.colleges.show', $college),
                ]));
            });

        $trainings = Training::query()
            ->where('status', '!=', 'Draft')
            ->where(function (Builder $query) use ($terms): void {
                foreach ($terms as $term) {
                    $query->orWhere('title', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                }
            })
            ->orderByDesc('start_date')
            ->limit(5)
            ->get()
            ->map(fn (Training $training): string => implode(' | ', array_filter([
                "Training: {$training->title}",
                "status: {$training->status}",
                $training->start_date?->format('Y-m-d'),
                $training->location_or_link,
                auth()->check() ? 'calendar: '.route('training.calendar') : null,
            ])));

        return $colleges->concat($trainings)->implode("\n");
    }
}
