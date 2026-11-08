@props([
    'badge' => null,
    'tier' => null,
    'size' => 64,
    'earned' => false,
    'progressCurrent' => null,
    'progressTotal' => null,
])

@php
    $badgeConfig = is_array($badge)
        ? $badge
        : collect(config('gamification.badges', []))->firstWhere('slug', $badge);

    $slug = (string) ($badgeConfig['slug'] ?? $badge ?? '');
    $badgeName = (string) ($badgeConfig['name_id'] ?? ucfirst($slug));
    $tierKey = (string) ($tier ?? 'default');
    $tierLabel = ucfirst($tierKey);
    $imagePath = (string) ($badgeConfig['images'][$tierKey] ?? $badgeConfig['images']['default'] ?? '');
    $alt = "Badge {$badgeName}, tingkat {$tierLabel}";
    $isLocked = ! $earned;
@endphp

<div class="badge-component" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
  <img
    class="{{ $isLocked ? 'badge-locked' : '' }}"
    src="{{ asset($imagePath) }}"
    alt="{{ $alt }}"
    width="{{ $size }}"
    height="{{ $size }}"
    loading="lazy"
    style="display: block; margin: 0 auto; object-fit: contain; {{ $isLocked ? 'filter: grayscale(1); opacity: .35;' : '' }}"
  >
  <div class="mt-2" style="text-align: center;">
    <span class="small fw-bold">{{ $tierLabel }}</span>
    @if ($isLocked)
      @if ($progressCurrent !== null && $progressTotal !== null)
        <div class="small text-secondary">{{ $progressCurrent }}/{{ $progressTotal }}</div>
      @endif
    @endif
  </div>
</div>

<style>
  .badge-component .badge-locked { transition: opacity .2s; }
  @media (prefers-reduced-motion: reduce) {
    .badge-component .badge-locked { transition: none !important; }
  }
</style>
