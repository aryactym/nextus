<?php

declare(strict_types=1);

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Jobs\ImportLearnersJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LearnerImportController extends Controller
{
    private const REQUIRED_HEADERS = ['name', 'email', 'password'];

    private const OPTIONAL_HEADERS = ['nama_jabatan', 'kategori_jabatan'];

    public function create(): View
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);

        return view('org.learners.import');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return back()->withErrors(['csv_file' => 'File tidak dapat dibaca.']);
        }

        // Baca dan validasi header
        $rawHeader = fgetcsv($handle);
        fclose($handle);

        if ($rawHeader === false || $rawHeader === null) {
            return back()->withErrors(['csv_file' => 'File CSV kosong atau tidak valid.']);
        }

        $header = array_map(fn (string $h) => strtolower(trim($h)), $rawHeader);

        foreach (self::REQUIRED_HEADERS as $required) {
            if (! in_array($required, $header, true)) {
                return back()->withErrors([
                    'csv_file' => "Kolom wajib \"{$required}\" tidak ditemukan. Pastikan header sesuai template.",
                ]);
            }
        }

        // Parse semua baris
        $rows = $this->parseRows($path, $header);

        if (count($rows) === 0) {
            return back()->withErrors(['csv_file' => 'File CSV tidak memiliki data (hanya header).']);
        }

        if (count($rows) > 500) {
            return back()->withErrors(['csv_file' => 'Maksimal 500 baris per upload.']);
        }

        // Dispatch job & simpan cache key di session
        $cacheKey = 'import_learners_'.auth()->id().'_'.Str::random(8);
        $orgId = (int) auth()->user()->organization_id;

        ImportLearnersJob::dispatchSync($orgId, $rows, $cacheKey);

        return redirect()->route('admin.org.learners.import.result', ['key' => $cacheKey]);
    }

    public function result(Request $request): View|RedirectResponse
    {
        abort_unless(auth()->user()->can('manage org learners'), 403);

        $key = $request->query('key', '');
        $results = Cache::get($key);

        if ($results === null) {
            return redirect()->route('admin.org.learners.import')
                ->with('info', 'Hasil import tidak ditemukan atau sudah kedaluwarsa.');
        }

        return view('org.learners.import-result', compact('results'));
    }

    public function template(): Response
    {
        $csv = implode(',', array_merge(self::REQUIRED_HEADERS, self::OPTIONAL_HEADERS))."\n";
        $csv .= "Ahmad Fauzi,ahmad@contoh.org,Password123!,Staf Program,Officer\n";
        $csv .= "Siti Rahma,siti@contoh.org,Password123!,,\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template-import-peserta.csv"',
        ]);
    }

    /**
     * @param  array<int, string>  $header
     * @return array<int, array<string, string>>
     */
    private function parseRows(string $path, array $header): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return [];
        }

        fgetcsv($handle); // skip header row

        $rows = [];
        while (($raw = fgetcsv($handle)) !== false) {
            if ($raw === null || (count($raw) === 1 && trim($raw[0]) === '')) {
                continue; // skip baris kosong
            }

            $row = [];
            foreach ($header as $i => $col) {
                $row[$col] = isset($raw[$i]) ? trim($raw[$i]) : '';
            }

            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }
}
