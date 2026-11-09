<?php

declare(strict_types=1);

namespace App\Http\Controllers\Org;

use App\Domain\Catalog\Models\Course;
use App\Http\Controllers\Controller;
use App\Services\OrgReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    private function service(): OrgReportService
    {
        return new OrgReportService((int) auth()->user()->organization_id);
    }

    private function filters(Request $request): array
    {
        return [
            'date_from' => (string) $request->input('date_from', ''),
            'date_to' => (string) $request->input('date_to', ''),
            'course_id' => $request->input('course_id') ? (int) $request->input('course_id') : null,
        ];
    }

    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('view org reports'), 403);

        $f = $this->filters($request);
        $summary = $this->service()->summary($f['date_from'] ?: null, $f['date_to'] ?: null, $f['course_id']);
        $courses = Course::withoutGlobalScopes()
            ->where('organization_id', auth()->user()->organization_id)
            ->where('status', 'published')
            ->orderBy('title->id')
            ->get(['id', 'uuid', 'title']);

        return view('org.reports.index', compact('summary', 'courses', 'f'));
    }

    public function byTopic(Request $request): View
    {
        abort_unless(auth()->user()->can('view org reports'), 403);

        $f = $this->filters($request);
        $rows = $this->service()->byTopic($f['date_from'] ?: null, $f['date_to'] ?: null);

        return view('org.reports.by-topic', compact('rows', 'f'));
    }

    public function byCourse(Request $request): View
    {
        abort_unless(auth()->user()->can('view org reports'), 403);

        $f = $this->filters($request);
        $rows = $this->service()->byCourse($f['date_from'] ?: null, $f['date_to'] ?: null);

        return view('org.reports.by-course', compact('rows', 'f'));
    }

    public function byCourseDetail(Request $request, string $courseUuid): View
    {
        abort_unless(auth()->user()->can('view org reports'), 403);

        $course = Course::withoutGlobalScopes()->where('uuid', $courseUuid)->firstOrFail();
        $f = $this->filters($request);
        $rows = $this->service()->byCourseDetail($courseUuid, $f['date_from'] ?: null, $f['date_to'] ?: null);

        return view('org.reports.by-course-detail', compact('course', 'rows', 'f'));
    }

    public function byLearner(Request $request): View
    {
        abort_unless(auth()->user()->can('view org reports'), 403);

        $f = $this->filters($request);
        $rows = $this->service()->byLearner($f['date_from'] ?: null, $f['date_to'] ?: null, $f['course_id']);
        $courses = Course::withoutGlobalScopes()
            ->where('organization_id', auth()->user()->organization_id)
            ->where('status', 'published')
            ->orderBy('title->id')
            ->get(['id', 'uuid', 'title']);

        return view('org.reports.by-learner', compact('rows', 'courses', 'f'));
    }

    public function exportCsv(Request $request, string $type): Response|RedirectResponse
    {
        abort_unless(auth()->user()->can('view org reports'), 403);

        $f = $this->filters($request);
        $service = $this->service();

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
