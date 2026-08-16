@php
    $statusClasses = [
        'available' => 'badge-success',
        'reserved' => 'badge-warning',
        'sold' => 'badge-danger',
        'hidden' => 'badge-muted',
    ];
    $statusClass = $statusClasses[$status->value ?? ''] ?? 'badge-muted';
@endphp
<span class="badge {{ $statusClass }}">{{ __($status->label()) }}</span>
