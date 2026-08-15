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
        $stopWords = ['college', 'govt', 'government', 'এই', 'এর', 'কোন', 'কি', 'কী', 'কোর্স', 'চালু', 'আছে', 'আমাকে', 'জানাও', 'সম্পর্কে', 'তথ্য', 'দিন'];
        $terms = collect(preg_split('/[^\pL\pN]+/u', Str::lower($question)) ?: [])
            ->filter(fn (string $term): bool => mb_strlen($term) >= 3)
            ->reject(fn (string $term): bool => in_array($term, $stopWords, true))
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
                    $query->orWhereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(college_code) LIKE ?', ["%{$term}%"]);
                }
            })
            ->with(['division:id,name,bn_name', 'district:id,name,bn_name', 'programs:id,college_id,level,name,items'])
            ->limit(50)
            ->get()
            ->map(function (College $college) use ($terms): array {
                $searchableName = Str::lower($college->name.' '.$college->college_code);
                $score = $terms->sum(fn (string $term): int => str_contains($searchableName, $term) ? mb_strlen($term) : 0);

                return ['college' => $college, 'score' => $score];
            })
            ->filter(fn (array $match): bool => $match['score'] > 0)
            ->sortByDesc('score')
            ->take(5)
            ->pluck('college')
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
                    $query->orWhereRaw('LOWER(title) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(description) LIKE ?', ["%{$term}%"]);
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
