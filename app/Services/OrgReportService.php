<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Learning\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrgReportService
{
    public function __construct(private readonly ?int $orgId) {}

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function jsonExtract(string $column, string $key): string
    {
        // SQLite: json_extract sudah return unquoted string
        // MySQL: perlu JSON_UNQUOTE(JSON_EXTRACT(...))
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return "json_extract({$column}, '$.{$key}')";
        }

        return "JSON_UNQUOTE(JSON_EXTRACT({$column}, '$.{$key}'))";
    }

    private function baseQuery(?string $dateFrom, ?string $dateTo, ?int $courseId): Builder
    {
        return Enrollment::query()
            ->when($this->orgId, fn ($q) => $q->whereHas(
                'user',
                fn ($uq) => $uq->withoutGlobalScopes()->where('organization_id', $this->orgId)
            ))
            ->when($dateFrom, fn ($q) => $q->whereDate('enrolled_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('enrolled_at', '<=', $dateTo))
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId));
    }

    // -----------------------------------------------------------------------
    // Summary
    // -----------------------------------------------------------------------

    /** @return array{learners: int, enrollments: int, completed: int, certificates: int, avg_progress: int} */
    public function summary(?string $dateFrom, ?string $dateTo, ?int $courseId): array
    {
        $learnerCount = User::withoutGlobalScopes()
            ->when($this->orgId, fn ($q) => $q->where('organization_id', $this->orgId))
            ->whereNull('deleted_at')
            ->count();

        $base = $this->baseQuery($dateFrom, $dateTo, $courseId);

        $enrollments = (clone $base)->count();
        $completed = (clone $base)->whereNotNull('completed_at')->count();
        $certificates = (clone $base)->whereHas('certificate')->count();
        $avgProgress = $enrollments > 0
            ? (int) round((clone $base)->avg('progress_percent') ?? 0)
            : 0;

        return compact('learnerCount', 'enrollments', 'completed', 'certificates', 'avgProgress');
    }

    // -----------------------------------------------------------------------
    // Per topic (category)
    // -----------------------------------------------------------------------

    /** @return Collection<int, object{category_name: string, enrolled: int, completed: int, avg_progress: int}> */
    public function byTopic(?string $dateFrom, ?string $dateTo): Collection
    {
        return DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->join('categories', 'courses.category_id', '=', 'categories.id')
            ->when($this->orgId, fn ($q) => $q->where('users.organization_id', $this->orgId))
            ->whereNull('users.deleted_at')
            ->whereNull('enrollments.deleted_at')
            ->whereNull('courses.deleted_at')
            ->whereNull('categories.deleted_at')
            ->when($dateFrom, fn ($q) => $q->whereDate('enrollments.enrolled_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('enrollments.enrolled_at', '<=', $dateTo))
            ->select(
                DB::raw($this->jsonExtract('categories.name', 'id').' as category_name'),
                DB::raw('COUNT(DISTINCT enrollments.id) as enrolled'),
                DB::raw('SUM(CASE WHEN enrollments.completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed'),
                DB::raw('ROUND(AVG(enrollments.progress_percent)) as avg_progress'),
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('enrolled')
            ->get();
    }

    // -----------------------------------------------------------------------
    // Per course
    // -----------------------------------------------------------------------

    /** @return Collection<int, object{course_title: string, category_name: string, enrolled: int, completed: int, avg_progress: int, certificates: int}> */
    public function byCourse(?string $dateFrom, ?string $dateTo): Collection
    {
        return DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->leftJoin('categories', 'courses.category_id', '=', 'categories.id')
            ->leftJoin('certificates', 'certificates.enrollment_id', '=', 'enrollments.id')
            ->when($this->orgId, fn ($q) => $q->where('users.organization_id', $this->orgId))
            ->whereNull('users.deleted_at')
            ->whereNull('enrollments.deleted_at')
            ->whereNull('courses.deleted_at')
            ->when($dateFrom, fn ($q) => $q->whereDate('enrollments.enrolled_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('enrollments.enrolled_at', '<=', $dateTo))
            ->select(
                'courses.uuid as course_uuid',
                DB::raw($this->jsonExtract('courses.title', 'id').' as course_title'),
                DB::raw('COALESCE('.$this->jsonExtract('categories.name', 'id').", '—') as category_name"),
                DB::raw('COUNT(DISTINCT enrollments.id) as enrolled'),
                DB::raw('SUM(CASE WHEN enrollments.completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed'),
                DB::raw('ROUND(AVG(enrollments.progress_percent)) as avg_progress'),
                DB::raw('COUNT(DISTINCT certificates.id) as certificates'),
            )
            ->groupBy('courses.id', 'courses.uuid', 'courses.title', 'categories.name')
            ->orderByDesc('enrolled')
            ->get();
    }

    // -----------------------------------------------------------------------
    // Per course drill-down (learner list for one course)
    // -----------------------------------------------------------------------

    /** @return Collection<int, object{learner_name: string, learner_email: string, progress_percent: int, completed_at: string|null, has_certificate: bool}> */
    public function byCourseDetail(string $courseUuid, ?string $dateFrom, ?string $dateTo): Collection
    {
        return DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->leftJoin('certificates', 'certificates.enrollment_id', '=', 'enrollments.id')
            ->when($this->orgId, fn ($q) => $q->where('users.organization_id', $this->orgId))
            ->where('courses.uuid', $courseUuid)
            ->whereNull('users.deleted_at')
            ->whereNull('enrollments.deleted_at')
            ->when($dateFrom, fn ($q) => $q->whereDate('enrollments.enrolled_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('enrollments.enrolled_at', '<=', $dateTo))
            ->select(
                'users.uuid as user_uuid',
                'users.name as learner_name',
                'users.email as learner_email',
                'enrollments.progress_percent',
                'enrollments.enrolled_at',
                'enrollments.completed_at',
                DB::raw('COUNT(certificates.id) > 0 as has_certificate'),
            )
            ->groupBy(
                'users.uuid', 'users.name', 'users.email',
                'enrollments.progress_percent', 'enrollments.enrolled_at', 'enrollments.completed_at',
            )
            ->orderBy('users.name')
            ->get();
    }

    // -----------------------------------------------------------------------
    // Per learner
    // -----------------------------------------------------------------------

    /** @return Collection<int, object{learner_name: string, learner_email: string, enrolled: int, completed: int, certificates: int, avg_progress: int}> */
    public function byLearner(?string $dateFrom, ?string $dateTo, ?int $courseId): Collection
    {
        return DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->leftJoin('certificates', 'certificates.enrollment_id', '=', 'enrollments.id')
            ->when($this->orgId, fn ($q) => $q->where('users.organization_id', $this->orgId))
            ->whereNull('users.deleted_at')
            ->whereNull('enrollments.deleted_at')
            ->when($dateFrom, fn ($q) => $q->whereDate('enrollments.enrolled_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('enrollments.enrolled_at', '<=', $dateTo))
            ->when($courseId, fn ($q) => $q->where('enrollments.course_id', $courseId))
            ->select(
                'users.uuid as user_uuid',
                'users.name as learner_name',
                'users.email as learner_email',
                'users.nama_jabatan',
                'users.kategori_jabatan',
                DB::raw('COUNT(DISTINCT enrollments.id) as enrolled'),
                DB::raw('SUM(CASE WHEN enrollments.completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed'),
                DB::raw('COUNT(DISTINCT certificates.id) as certificates'),
                DB::raw('ROUND(AVG(enrollments.progress_percent)) as avg_progress'),
            )
            ->groupBy('users.id', 'users.uuid', 'users.name', 'users.email', 'users.nama_jabatan', 'users.kategori_jabatan')
            ->orderBy('users.name')
            ->get();
    }

    // -----------------------------------------------------------------------
    // CSV helpers
    // -----------------------------------------------------------------------

    public function exportTopicCsv(Collection $rows): string
    {
        $lines = ["Topik,Peserta terdaftar,Selesai,Rata-rata progres\n"];
        foreach ($rows as $r) {
            $lines[] = implode(',', [
                '"'.$r->category_name.'"',
                $r->enrolled,
                $r->completed,
                $r->avg_progress.'%',
            ])."\n";
        }

        return implode('', $lines);
    }

    public function exportCourseCsv(Collection $rows): string
    {
        $lines = ["Kursus,Topik,Peserta,Selesai,Rata-rata progres,Sertifikat\n"];
        foreach ($rows as $r) {
            $lines[] = implode(',', [
                '"'.$r->course_title.'"',
                '"'.$r->category_name.'"',
                $r->enrolled,
                $r->completed,
                $r->avg_progress.'%',
                $r->certificates,
            ])."\n";
        }

        return implode('', $lines);
    }

    public function exportLearnerCsv(Collection $rows): string
    {
        $lines = ["Nama,Email,Jabatan,Kursus diikuti,Selesai,Sertifikat,Rata-rata progres\n"];
        foreach ($rows as $r) {
            $lines[] = implode(',', [
                '"'.$r->learner_name.'"',
                '"'.$r->learner_email.'"',
                '"'.($r->nama_jabatan ?? '').'"',
                $r->enrolled,
                $r->completed,
                $r->certificates,
                $r->avg_progress.'%',
            ])."\n";
        }

        return implode('', $lines);
    }
}
