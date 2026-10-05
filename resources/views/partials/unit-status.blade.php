@php
    $statusClasses = [
        'available' => 'badge-success',
        'reserved' => 'badge-warning',
        'sold' => 'badge-sold',
        'hidden' => 'badge-muted',
    ];
    $statusValue = is_object($status) ? ($status->value ?? '') : (string) ($status ?? '');
    $statusClass = $statusClasses[$statusValue] ?? 'badge-muted';
    $statusLabel = match ($statusValue) {
        'available' => __('متاح'),
        'reserved' => __('محجوز'),
        'sold' => __('مباع'),
        'hidden' => __('مخفي'),
        default => __('—'),
    };
@endphp
<span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
