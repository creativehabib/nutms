<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\Training;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WebsiteKnowledgeService
{
    public function context(string $question): string
    {
        $stopWords = ['college', 'colleges', 'govt', 'government', 'please', 'share', 'me', 'details', 'detail', 'information', 'about', 'give', 'tell', 'show', 'want', 'know', 'এই', 'এর', 'কোন', 'কি', 'কী', 'কোর্স', 'চালু', 'আছে', 'আমাকে', 'জানাও', 'সম্পর্কে', 'তথ্য', 'দিন', 'দাও', 'কলেজ', 'বিষয়', 'বিষয়গুলো'];
        $baseTerms = collect(preg_split('/[^\pL\pN]+/u', Str::lower($question)) ?: [])
            ->filter(fn (string $term): bool => mb_strlen($term) >= 3)
            ->reject(fn (string $term): bool => in_array($term, $stopWords, true))
            ->unique()
            ->take(8)
            ->values()
            ->all();
        $terms = collect($this->expandTerms($baseTerms));

        if ($terms->isEmpty()) {
            return '';
        }

        $collegeIds = $this->matchingCollegeIds($terms->all());
        $colleges = College::query()
            ->whereKey($collegeIds)
            ->with(['division:id,name,bn_name', 'district:id,name,bn_name', 'programs:id,college_id,level,name,items'])
            ->get()
            ->sortBy(function (College $college) use ($collegeIds): int {
                $position = array_search($college->id, $collegeIds, true);

                return $position === false ? PHP_INT_MAX : $position;
            })
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
                    'verified profile link: '.route('public.colleges.index', ['college' => $college->id]),
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

    /**
     * @param  array<int, string>  $questionTerms
     * @return array<int, int>
     */
    private function matchingCollegeIds(array $questionTerms): array
    {
        $buildIndex = fn () => College::query()
            ->where('is_active', true)
            ->where('approval_status', ApprovalStatus::Approved)
            ->get(['id', 'name', 'college_code'])
            ->map(fn (College $college): array => [
                'id' => $college->id,
                'terms' => $this->searchTerms($college->name.' '.$college->college_code),
            ])->all();
        $indexVersion = College::query()->where('is_active', true)->where('approval_status', ApprovalStatus::Approved)
            ->selectRaw('COUNT(*) as total, MAX(updated_at) as latest_update')
            ->first();
        $cacheVersion = sha1((string) $indexVersion?->total.'|'.(string) $indexVersion?->latest_update);
        $cacheKey = 'ai:public-college-search-index:'.$cacheVersion;
        $index = app()->runningUnitTests()
            ? $buildIndex()
            : Cache::remember($cacheKey, now()->addMinutes(10), $buildIndex);

        return collect($index)
            ->map(function (array $college) use ($questionTerms): array {
                $matches = collect($questionTerms)->map(fn (string $questionTerm): float => collect($college['terms'])
                    ->map(fn (string $collegeTerm): float => $this->termSimilarity($questionTerm, $collegeTerm))
                    ->max() ?? 0.0);
                $strongMatches = $matches->filter(fn (float $score): bool => $score >= 0.68);
                $score = $strongMatches->sum() + ($strongMatches->count() * 0.35);

                return ['id' => $college['id'], 'score' => $score];
            })
            ->filter(fn (array $college): bool => $college['score'] >= 1.0)
            ->sortByDesc('score')
            ->take(5)
            ->pluck('id')
            ->all();
    }

    /** @return array<int, string> */
    private function searchTerms(string $value): array
    {
        $terms = collect(preg_split('/[^\pL\pN]+/u', Str::lower($value)) ?: [])
            ->filter(fn (string $term): bool => mb_strlen($term) >= 3)
            ->reject(fn (string $term): bool => in_array($term, ['college', 'govt', 'government'], true))
            ->values()
            ->all();

        return $this->expandTerms($terms);
    }

    /**
     * @param  array<int, string>  $terms
     * @return array<int, string>
     */
    private function expandTerms(array $terms): array
    {
        $expanded = $terms;
        $termCount = count($terms);

        for ($size = 2; $size <= min(4, $termCount); $size++) {
            for ($offset = 0; $offset <= $termCount - $size; $offset++) {
                $expanded[] = implode('', array_slice($terms, $offset, $size));
            }
        }

        return array_values(array_unique($expanded));
    }

    private function termSimilarity(string $first, string $second): float
    {
        if ($first === $second) {
            return 1.0;
        }

        if (is_numeric($first) || is_numeric($second)) {
            return 0.0;
        }

        $maximumLength = max(mb_strlen($first), mb_strlen($second));

        return $maximumLength === 0 ? 0.0 : 1 - ($this->unicodeDistance($first, $second) / $maximumLength);
    }

    private function unicodeDistance(string $first, string $second): int
    {
        $firstCharacters = mb_str_split($first);
        $secondCharacters = mb_str_split($second);
        $previousRow = range(0, count($secondCharacters));

        foreach ($firstCharacters as $firstIndex => $firstCharacter) {
            $currentRow = [$firstIndex + 1];
            foreach ($secondCharacters as $secondIndex => $secondCharacter) {
                $currentRow[] = min(
                    $currentRow[$secondIndex] + 1,
                    $previousRow[$secondIndex + 1] + 1,
                    $previousRow[$secondIndex] + ($firstCharacter === $secondCharacter ? 0 : 1),
                );
            }
            $previousRow = $currentRow;
        }

        return $previousRow[count($secondCharacters)];
    }
}
