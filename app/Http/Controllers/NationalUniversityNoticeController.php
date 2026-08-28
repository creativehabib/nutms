<?php

namespace App\Http\Controllers;

use App\Services\NationalUniversityNoticeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class NationalUniversityNoticeController extends Controller
{
    public function __construct(public NationalUniversityNoticeService $noticeService) {}

    public function __invoke(Request $request): View
    {
        $search = Str::of((string) $request->query('search'))->squish()->limit(150, '')->toString();
        $category = Str::of((string) $request->query('category'))->squish()->limit(100, '')->toString();
        $allNotices = collect($this->noticeService->all());
        $totalNotices = $allNotices->count();
        $categories = $this->categories($allNotices);

        $filteredNotices = $allNotices
            ->when($search !== '', fn (Collection $notices): Collection => $notices->filter(
                fn (array $notice): bool => Str::contains($notice['title'].' '.($notice['category'] ?? ''), $search, true)
            ))
            ->when($category !== '', fn (Collection $notices): Collection => $notices->where('category', $category))
            ->values();

        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $notices = new LengthAwarePaginator(
            items: $filteredNotices->forPage($currentPage, $perPage)->values(),
            total: $filteredNotices->count(),
            perPage: $perPage,
            currentPage: $currentPage,
            options: [
                'path' => route('notices.index'),
                'query' => $request->except('page'),
            ],
        );

        return view('notices.index', compact('notices', 'categories', 'search', 'category', 'totalNotices'));
    }

    /**
     * @return array<string, int>
     */
    private function categories(Collection $notices): array
    {
        return $notices
            ->pluck('category')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->all();
    }
}
