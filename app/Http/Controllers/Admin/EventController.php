<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Catalog\Models\Course;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    private function courses(): Collection
    {
        return Course::withoutGlobalScopes()
            ->where('status', 'published')
            ->orderBy('title->id')
            ->get(['id', 'title']);
    }

    public function index(Request $request): View
    {
        abort_unless(auth()->user()->can('manage events'), 403);

        $q = trim((string) $request->input('q', ''));
        $type = (string) $request->input('type', '');
        $status = (string) $request->input('status', '');

        $events = Event::withoutGlobalScopes()
            ->with('course')
            ->when($q !== '', fn ($qr) => $qr->where('title', 'like', '%'.$q.'%'))
            ->when($type !== '', fn ($qr) => $qr->where('type', $type))
            ->when($status !== '', fn ($qr) => $qr->where('status', $status))
            ->orderBy('starts_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.events.index', compact('events', 'q', 'type', 'status'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('manage events'), 403);

        return view('admin.events.create', ['courses' => $this->courses()]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage events'), 403);

        $data = $this->validated($request);
        $data['organization_id'] = auth()->user()->organization_id;

        Event::create($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Event $event): View
    {
        abort_unless(auth()->user()->can('manage events'), 403);

        return view('admin.events.edit', ['event' => $event, 'courses' => $this->courses()]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage events'), 403);

        $event->update($this->validated($request, $event));

        return redirect()->route('admin.events.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage events'), 403);

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Event $event = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:webinar,pelatihan,batch'],
            'format' => ['required', 'in:daring,luring'],
            'status' => ['required', 'in:draft,open,soon,full,closed'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'quota' => ['nullable', 'integer', 'min:1'],
            'registration_url' => ['nullable', 'url', 'max:500'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
