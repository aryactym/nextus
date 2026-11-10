<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Http\Controllers;

use App\Domain\Catalog\Models\CourseModule;
use App\Domain\Catalog\Models\Lesson;
use App\Domain\Learning\Models\Quiz;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuizImportController extends Controller
{
    /**
     * Download template Excel for quiz questions.
     */
    public function downloadTemplate(Request $request): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Soal');

        $headers = ['Pertanyaan', 'Pilihan A', 'Pilihan B', 'Pilihan C', 'Pilihan D', 'Jawaban Benar (A/B/C/D)'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue(chr(65 + $i).'1', $header);
        }

        // Example row
        $sheet->setCellValue('A2', 'Apa ibukota Indonesia?');
        $sheet->setCellValue('B2', 'Jakarta');
        $sheet->setCellValue('C2', 'Bandung');
        $sheet->setCellValue('D2', 'Surabaya');
        $sheet->setCellValue('E2', 'Medan');
        $sheet->setCellValue('F2', 'A');

        // Style header
        foreach (range('A', 'F') as $col) {
            $sheet->getStyle($col.'1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template-soal-quiz.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import quiz questions from Excel for a module (pre/post test).
     */
    public function importModule(Request $request, CourseModule $module, string $type): RedirectResponse
    {
        abort_unless($request->user()->can('manage courses'), 403);
        abort_unless(in_array($type, ['pre', 'post'], true), 422);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $quiz = Quiz::firstOrCreate([
            'module_id' => $module->id,
            'type' => $type,
        ]);

        $imported = 0;
        $errors = [];

        foreach ($rows as $rowNum => $row) {
            if ($rowNum === 1) {
                continue; // skip header
            }

            $question = trim((string) ($row['A'] ?? ''));
            if ($question === '') {
                continue;
            }

            $options = [
                trim((string) ($row['B'] ?? '')),
                trim((string) ($row['C'] ?? '')),
                trim((string) ($row['D'] ?? '')),
                trim((string) ($row['E'] ?? '')),
            ];

            // Remove empty options
            $options = array_values(array_filter($options, fn ($o) => $o !== ''));
            if (count($options) < 2) {
                $errors[] = "Baris {$rowNum}: minimal 2 pilihan jawaban.";

                continue;
            }

            $answerLetter = strtoupper(trim((string) ($row['F'] ?? '')));
            $answerMap = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
            if (! isset($answerMap[$answerLetter]) || $answerMap[$answerLetter] >= count($options)) {
                $errors[] = "Baris {$rowNum}: jawaban benar tidak valid ({$answerLetter}).";

                continue;
            }

            $quiz->questions()->create([
                'question' => $question,
                'options' => $options,
                'correct_index' => $answerMap[$answerLetter],
                'sort' => ($quiz->questions()->max('sort') ?? 0) + 1,
            ]);

            $imported++;
        }

        $msg = "Import selesai: {$imported} soal ditambahkan.";
        if (! empty($errors)) {
            $msg .= ' Error: '.implode(' ', $errors);
        }

        return redirect()
            ->route('admin.courses.edit', $module->course)
            ->with('success', $msg);
    }

    /**
     * Import quiz questions from Excel for a lesson quiz.
     */
    public function importLesson(Request $request, Lesson $lesson): RedirectResponse
    {
        abort_unless($request->user()->can('manage courses'), 403);

        $lesson = Lesson::query()
            ->whereHas('module.course', fn ($q) => $q->forOrganization())
            ->findOrFail($lesson->getKey());

        abort_unless($lesson->type === 'quiz', 422);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $quiz = Quiz::firstOrCreate(['lesson_id' => $lesson->id]);

        $imported = 0;
        $errors = [];

        foreach ($rows as $rowNum => $row) {
            if ($rowNum === 1) {
                continue;
            }

            $question = trim((string) ($row['A'] ?? ''));
            if ($question === '') {
                continue;
            }

            $options = [
                trim((string) ($row['B'] ?? '')),
                trim((string) ($row['C'] ?? '')),
                trim((string) ($row['D'] ?? '')),
                trim((string) ($row['E'] ?? '')),
            ];

            $options = array_values(array_filter($options, fn ($o) => $o !== ''));
            if (count($options) < 2) {
                $errors[] = "Baris {$rowNum}: minimal 2 pilihan jawaban.";

                continue;
            }

            $answerLetter = strtoupper(trim((string) ($row['F'] ?? '')));
            $answerMap = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
            if (! isset($answerMap[$answerLetter]) || $answerMap[$answerLetter] >= count($options)) {
                $errors[] = "Baris {$rowNum}: jawaban benar tidak valid ({$answerLetter}).";

                continue;
            }

            $quiz->questions()->create([
                'question' => $question,
                'options' => $options,
                'correct_index' => $answerMap[$answerLetter],
                'sort' => ($quiz->questions()->max('sort') ?? 0) + 1,
            ]);

            $imported++;
        }

        $msg = "Import selesai: {$imported} soal ditambahkan.";
        if (! empty($errors)) {
            $msg .= ' Error: '.implode(' ', $errors);
        }

        return redirect()
            ->route('admin.courses.edit', $lesson->module->course)
            ->with('success', $msg);
    }
}
