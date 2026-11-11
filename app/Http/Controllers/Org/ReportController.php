<?php

declare(strict_types=1);

namespace App\Http\Controllers\Org;

use App\Domain\Catalog\Models\Course;
use App\Domain\Organization\Models\Organization;
use App\Http\Controllers\Controller;
use App\Services\OrgReportService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    private function service(?int $orgId): OrgReportService
    {
        return new OrgReportService($orgId);
    }

    /**
     * @return array{date_from: string, date_to: string, course_id: int|null, org_id: int|null}
     */
    private function filters(Request $request): array
    {
        return [
            'date_from' => (string) $request->input('date_from', ''),
            'date_to' => (string) $request->input('date_to', ''),
            'course_id' => $request->input('course_id') ? (int) $request->input('course_id') : null,
            'org_id' => $request->input('org_id') ? (int) $request->input('org_id') : null,
        ];
    }

    /**
     * Resolve which org to report on.
     * Super Admin / Admin (manage users) can pick any org via ?org_id=, or leave it
     * empty (or pick "Semua organisasi") to aggregate across all organizations.
     * Org Admin always sees their own org only.
     *
     * @param  array{org_id: int|null}  $f
     */
    private function resolvedOrgId(array $f): ?int
    {
        if (auth()->user()->can('manage users') || auth()->user()->can('manage organizations')) {
            return $f['org_id'];
        }

        return (int) auth()->user()->organization_id;
    }

    /** Only Super Admin / Admin sees the org dropdown. */
    private function orgsForFilter(): Collection
    {
        if (! auth()->user()->can('manage users')) {
            return Organization::whereNull('deleted_at')->where('id', '')->get(); // empty
        }

        return Organization::whereNull('deleted_at')
            ->where('status', 'active')
            ->has('users')
            ->orderBy('name->id')
            ->get(['id', 'name', 'type']);
    }

    private function coursesForOrg(?int $orgId): Collection
    {
        return Course::withoutGlobalScopes()
            ->when($orgId, fn ($q) => $q->where('organization_id', $orgId))
            ->where('status', 'published')
            ->orderBy('title->id')
            ->get(['id', 'uuid', 'title']);
    }

    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('view org reports'), 403);

        $f = $this->filters($request);
        $orgId = $this->resolvedOrgId($f);
        $orgs = $this->orgsForFilter();
        $courses = $this->coursesForOrg($orgId);
        $summary = $this->service($orgId)->summary($f['date_from'] ?: null, $f['date_to'] ?: null, $f['course_id']);

        return view('org.reports.index', compact('summary', 'courses', 'orgs', 'f', 'orgId'));
    }

    public function byTopic(Request $request): View
    {
        abort_unless(auth()->user()->can('view org reports'), 403);

        $f = $this->filters($request);
        $orgId = $this->resolvedOrgId($f);
        $orgs = $this->orgsForFilter();
        $rows = $this->service($orgId)->byTopic($f['date_from'] ?: null, $f['date_to'] ?: null);

        return view('org.reports.by-topic', compact('rows', 'orgs', 'f', 'orgId'));
    }

    public function byCourse(Request $request): View
    {
        abort_unless(auth()->user()->can('view org reports'), 403);

        $f = $this->filters($request);
        $orgId = $this->resolvedOrgId($f);
        $orgs = $this->orgsForFilter();
        $rows = $this->service($orgId)->byCourse($f['date_from'] ?: null, $f['date_to'] ?: null);

        return view('org.reports.by-course', compact('rows', 'orgs', 'f', 'orgId'));
    }

    public function byCourseDetail(Request $request, string $courseUuid): View
    {
        abort_unless(auth()->user()->can('view org reports'), 403);

        $f = $this->filters($request);
        $orgId = $this->resolvedOrgId($f);
        $course = Course::withoutGlobalScopes()->where('uuid', $courseUuid)->firstOrFail();
        $rows = $this->service($orgId)->byCourseDetail($courseUuid, $f['date_from'] ?: null, $f['date_to'] ?: null);

        return view('org.reports.by-course-detail', compact('course', 'rows', 'f', 'orgId'));
    }

    public function byLearner(Request $request): View
    {
        abort_unless(auth()->user()->can('view org reports'), 403);

        $f = $this->filters($request);
        $orgId = $this->resolvedOrgId($f);
        $orgs = $this->orgsForFilter();
        $courses = $this->coursesForOrg($orgId);
        $rows = $this->service($orgId)->byLearner($f['date_from'] ?: null, $f['date_to'] ?: null, $f['course_id']);

        return view('org.reports.by-learner', compact('rows', 'courses', 'orgs', 'f', 'orgId'));
    }

    public function exportCsv(Request $request, string $type): Response|RedirectResponse
    {
        abort_unless(auth()->user()->can('view org reports'), 403);

        $f = $this->filters($request);
        $orgId = $this->resolvedOrgId($f);
        $service = $this->service($orgId);

        [$csv, $filename] = match ($type) {
            'topic' => [$service->exportTopicCsv($service->byTopic($f['date_from'] ?: null, $f['date_to'] ?: null)), 'laporan-topik.csv'],
            'course' => [$service->exportCourseCsv($service->byCourse($f['date_from'] ?: null, $f['date_to'] ?: null)), 'laporan-kursus.csv'],
            'learner' => [$service->exportLearnerCsv($service->byLearner($f['date_from'] ?: null, $f['date_to'] ?: null, $f['course_id'])), 'laporan-peserta.csv'],
            default => abort(404),
        };

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
