<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Detail jadwal</h3></div>
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label" for="title">Judul</label>
                    <input type="text" id="title" name="title" form="event-form"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $event->title ?? '') }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="type">Jenis</label>
                        <select id="type" name="type" form="event-form"
                                class="form-select @error('type') is-invalid @enderror" required>
                            @foreach (['webinar' => 'Webinar', 'pelatihan' => 'Pelatihan', 'batch' => 'Batch kursus'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('type', $event->type ?? 'webinar') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="format">Format</label>
                        <select id="format" name="format" form="event-form"
                                class="form-select @error('format') is-invalid @enderror" required>
                            <option value="daring"  @selected(old('format', $event->format ?? 'daring') === 'daring')>Daring</option>
                            <option value="luring"  @selected(old('format', $event->format ?? '') === 'luring')>Luring</option>
                        </select>
                        @error('format') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="status">Status</label>
                        <select id="status" name="status" form="event-form"
                                class="form-select @error('status') is-invalid @enderror" required>
                            @foreach (['draft' => 'Draf', 'open' => 'Pendaftaran dibuka', 'soon' => 'Segera dibuka', 'full' => 'Penuh', 'closed' => 'Selesai'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('status', $event->status ?? 'open') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-0">
                    <div class="col-md-6">
                        <label class="form-label" for="starts_at">Mulai</label>
                        <input type="datetime-local" id="starts_at" name="starts_at" form="event-form"
                               class="form-control @error('starts_at') is-invalid @enderror"
                               value="{{ old('starts_at', isset($event) ? $event->starts_at->format('Y-m-d\TH:i') : '') }}" required>
                        @error('starts_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="ends_at">Selesai</label>
                        <input type="datetime-local" id="ends_at" name="ends_at" form="event-form"
                               class="form-control @error('ends_at') is-invalid @enderror"
                               value="{{ old('ends_at', isset($event) ? $event->ends_at->format('Y-m-d\TH:i') : '') }}" required>
                        @error('ends_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-0">
                    <div class="col-md-8">
                        <label class="form-label" for="location">Lokasi</label>
                        <input type="text" id="location" name="location" form="event-form"
                               class="form-control @error('location') is-invalid @enderror"
                               value="{{ old('location', $event->location ?? '') }}"
                               placeholder="cth: Zoom, Jakarta, Platform NextUs">
                        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="quota">Kuota peserta</label>
                        <input type="number" id="quota" name="quota" form="event-form"
                               class="form-control @error('quota') is-invalid @enderror"
                               value="{{ old('quota', $event->quota ?? '') }}" min="1" placeholder="Opsional">
                        @error('quota') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label" for="registration_url">URL pendaftaran</label>
                    <input type="url" id="registration_url" name="registration_url" form="event-form"
                           class="form-control @error('registration_url') is-invalid @enderror"
                           value="{{ old('registration_url', $event->registration_url ?? '') }}"
                           placeholder="https://...">
                    <div class="form-hint">Link ke form pendaftaran (Google Form, Eventbrite, dll.) atau halaman kursus.</div>
                    @error('registration_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="course_id">Kursus terkait</label>
                    <select id="course_id" name="course_id" form="event-form" class="form-select">
                        <option value="">Tidak ada / event terpisah</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}"
                                @selected(old('course_id', $event->course_id ?? '') == $course->id)>
                                {{ $course->getTranslation('title', 'id') }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Opsional. Hubungkan ke kursus untuk batch enrollment.</div>
                </div>

                <div class="mb-0">
                    <label class="form-label" for="description">Deskripsi</label>
                    <textarea id="description" name="description" form="event-form" rows="5"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $event->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" form="event-form" class="btn btn-primary">
                {{ isset($event) ? 'Simpan perubahan' : 'Tambah jadwal' }}
            </button>
            <a href="{{ route('admin.events.index') }}" class="btn btn-link">Batal</a>
        </div>
    </div>
</div>
