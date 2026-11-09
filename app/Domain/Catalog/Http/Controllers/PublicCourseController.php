<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Http\Controllers;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Course;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCourseController extends Controller
{
    /**
     * @var array<string, array{min: int, max: int|null}>
     */
    private const DURATION_BUCKETS = [
        's' => ['min' => 1, 'max' => 179],
        'm' => ['min' => 180, 'max' => 300],
        'l' => ['min' => 301, 'max' => null],
    ];

    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->input('q', '')),
            'kategori' => $this->arrayInput($request, 'kategori'),
            'level' => $this->arrayInput($request, 'level'),
            'akses' => $this->arrayInput($request, 'akses'),
            'durasi' => $this->arrayInput($request, 'durasi'),
        ];

        $query = $this->applyFilters($this->baseQuery($filters['q']), $filters);

        match ($request->input('sort', 'az')) {
            'terbaru' => $query->orderByDesc('published_at'),
            'short' => $query->orderBy('duration_minutes'),
            'long' => $query->orderByDesc('duration_minutes'),
            default => $query->orderBy('title'),
        };

        $courses = $query->with('category')->withCount('enrollments')->paginate(12);

        $categories = Category::withoutGlobalScope('organization')
            ->withCount(['courses' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('sort')
            ->get();

        $counts = $this->facetCounts($filters);

        $hasActiveFilters = $filters['q'] !== '' || collect($filters)
            ->except('q')
            ->contains(fn (array $values): bool => $values !== []);

        return view('catalog.public.index', compact(
            'courses',
            'categories',
            'counts',
            'filters',
            'hasActiveFilters',
        ));
    }

    public function show(string $slug): View|RedirectResponse
    {
        $course = Course::withoutGlobalScope('organization')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with(['category', 'organization', 'modules.lessons', 'requirement.prerequisiteCourse', 'requirement.pendidikan'])
            ->firstOrFail();

        $enrollment = null;
        if (auth()->check()) {
            $enrollment = $course->enrollments()
                ->where('user_id', auth()->id())
                ->first();
        }

        $isEnrolled = $enrollment !== null;

        return view('catalog.public.show', compact('course', 'isEnrolled', 'enrollment'));
    }

    /**
     * @return array<int, string>
     */
    private function arrayInput(Request $request, string $key): array
    {
        return array_values(array_filter(
            (array) $request->input($key, []),
            fn (mixed $value): bool => is_string($value) && $value !== '',
        ));
    }

    private function baseQuery(string $search): Builder
    {
        $query = Course::withoutGlobalScope('organization')
            ->where('status', 'published');

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * @param  array<string, array<int, string>|string>  $filters
     */
    private function applyFilters(Builder $query, array $filters, ?string $except = null): Builder
    {
        if ($except !== 'kategori' && $filters['kategori'] !== []) {
            $query->whereHas('category', fn (Builder $q) => $q->whereIn('slug', $filters['kategori']));
        }

        if ($except !== 'level' && $filters['level'] !== []) {
            $query->whereIn('level', $filters['level']);
        }

        if ($except !== 'akses' && $filters['akses'] !== []) {
            $query->whereIn('access_type', $filters['akses']);
        }

        if ($except !== 'durasi' && $filters['durasi'] !== []) {
            $query->where(function (Builder $q) use ($filters): void {
                foreach ($filters['durasi'] as $bucket) {
                    if (! isset(self::DURATION_BUCKETS[$bucket])) {
                        continue;
                    }

                    $range = self::DURATION_BUCKETS[$bucket];

                    $q->orWhere(function (Builder $rangeQuery) use ($range): void {
                        $rangeQuery->where('duration_minutes', '>=', $range['min']);

                        if ($range['max'] !== null) {
                            $rangeQuery->where('duration_minutes', '<=', $range['max']);
                        }
                    });
                }
            });
        }

        return $query;
    }

    /**
     * Hitung jumlah kursus per opsi filter dengan filter lain tetap diterapkan.
     *
     * @param  array<string, array<int, string>|string>  $filters
     * @return array<string, array<string, int>>
     */
    private function facetCounts(array $filters): array
    {
        $categoryCounts = $this->applyFilters($this->baseQuery($filters['q']), $filters, 'kategori')
            ->join('categories', 'courses.category_id', '=', 'categories.id')
            ->whereNull('categories.deleted_at')
            ->selectRaw('categories.slug as value, COUNT(*) as aggregate')
            ->groupBy('categories.slug')
            ->pluck('aggregate', 'value')
            ->map(fn (mixed $count): int => (int) $count)
            ->all();

        $levelCounts = $this->applyFilters($this->baseQuery($filters['q']), $filters, 'level')
            ->selectRaw('level as value, COUNT(*) as aggregate')
            ->groupBy('level')
            ->pluck('aggregate', 'value')
            ->map(fn (mixed $count): int => (int) $count)
            ->all();

        $accessCounts = $this->applyFilters($this->baseQuery($filters['q']), $filters, 'akses')
            ->selectRaw('access_type as value, COUNT(*) as aggregate')
            ->groupBy('access_type')
            ->pluck('aggregate', 'value')
            ->map(fn (mixed $count): int => (int) $count)
            ->all();

        $durationCounts = $this->applyFilters($this->baseQuery($filters['q']), $filters, 'durasi')
            ->selectRaw(
                'SUM(CASE WHEN duration_minutes BETWEEN 1 AND 179 THEN 1 ELSE 0 END) as s, '
                .'SUM(CASE WHEN duration_minutes BETWEEN 180 AND 300 THEN 1 ELSE 0 END) as m, '
                .'SUM(CASE WHEN duration_minutes > 300 THEN 1 ELSE 0 END) as l'
            )
            ->first();

        return [
            'kategori' => $categoryCounts,
            'level' => $levelCounts,
            'akses' => $accessCounts,
            'durasi' => [
                's' => (int) ($durationCounts->s ?? 0),
                'm' => (int) ($durationCounts->m ?? 0),
                'l' => (int) ($durationCounts->l ?? 0),
            ],
        ];
    }
}
