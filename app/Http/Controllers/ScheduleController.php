<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));
        $type = (string) $request->input('type', '');
        $format = (string) $request->input('format', '');
        $status = (string) $request->input('status', '');
        $showPast = (bool) $request->boolean('past', false);

        $query = Event::withoutGlobalScopes()
            ->published()
            ->with('course')
            ->when(! $showPast, fn ($q) => $q->upcoming())
            ->when($showPast && ! $request->has('past'), fn ($q) => $q->upcoming())
            ->when($q !== '', fn ($qr) => $qr->where('title', 'like', '%'.$q.'%'))
            ->when($type !== '', fn ($qr) => $qr->where('type', $type))
            ->when($format !== '', fn ($qr) => $qr->where('format', $format))
            ->when($status !== '', fn ($qr) => $qr->where('status', $status))
            ->orderBy('starts_at');

        // Group by month for display
        $events = $query->get();
        $grouped = $events->groupBy(fn ($e) => $e->starts_at->translatedFormat('F Y'));

        return view('pages.jadwal', compact('grouped', 'events', 'q', 'type', 'format', 'status', 'showPast'));
    }

    public function ics(string $uuid): Response
    {
        $event = Event::withoutGlobalScopes()
            ->published()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $ics = $this->buildIcs($event);

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$this->icsFilename($event).'"',
        ]);
    }

    private function buildIcs(Event $event): string
    {
        $dtStart = $event->starts_at->utc()->format('Ymd\THis\Z');
        $dtEnd = $event->ends_at->utc()->format('Ymd\THis\Z');
        $dtStamp = now()->utc()->format('Ymd\THis\Z');
        $summary = $this->icsEscape($event->title);
        $location = $this->icsEscape($event->location ?? '');
        $desc = $this->icsEscape(strip_tags($event->description ?? ''));
        $url = $event->registration_url ?? url('/jadwal');

        return "BEGIN:VCALENDAR\r\n"
            ."VERSION:2.0\r\n"
            ."PRODID:-//NextUs//Jadwal//ID\r\n"
            ."BEGIN:VEVENT\r\n"
            ."UID:{$event->uuid}@nextus\r\n"
            ."DTSTAMP:{$dtStamp}\r\n"
            ."DTSTART:{$dtStart}\r\n"
            ."DTEND:{$dtEnd}\r\n"
            ."SUMMARY:{$summary}\r\n"
            ."LOCATION:{$location}\r\n"
            ."DESCRIPTION:{$desc}\r\n"
            ."URL:{$url}\r\n"
            ."END:VEVENT\r\n"
            ."END:VCALENDAR\r\n";
    }

    private function icsEscape(string $value): string
    {
        return str_replace(["\r\n", "\n", "\r", ',', ';', '\\'], ['\\n', '\\n', '\\n', '\\,', '\\;', '\\\\'], $value);
    }

    private function icsFilename(Event $event): string
    {
        return Str::slug($event->title).'.ics';
    }
}
