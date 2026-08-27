<?php

namespace App\Http\Controllers;

use App\Models\College;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PublicCollegeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        $colleges = $this->publicColleges()
            ->with(['division:id,name,bn_name', 'district:id,name,bn_name', 'programs:id,college_id,level,name,items'])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('college_code', 'like', "%{$search}%")
                        ->orWhereHas('programs', function (Builder $query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('items', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('public-colleges.index', compact('colleges', 'search'));
    }

    public function show(College $college): View
    {
        abort_unless(
            $college->isPubliclyVisible(),
            404,
        );

        $college->load(['division:id,name,bn_name', 'district:id,name,bn_name', 'thana:id,name,bn_name', 'programs'])
            ->loadCount('teachers');

        return view('public-colleges.show', compact('college'));
    }

    /** @return Builder<College> */
    private function publicColleges(): Builder
    {
        return College::query()->publiclyVisible();
    }
}
