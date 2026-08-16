@php
    // Unified status → color mapping (works for leads, deals, tasks, offers, reservations, follow-ups)
    $statusMap = [
        // Lead stages
        'new'          => ['bg' => 'bg-emerald-500/15', 'text' => 'text-emerald-300', 'border' => 'border-emerald-500/25'],
        'contacted'    => ['bg' => 'bg-sky-500/15',     'text' => 'text-sky-300',     'border' => 'border-sky-500/25'],
        'interested'   => ['bg' => 'bg-violet-500/15',  'text' => 'text-violet-300',  'border' => 'border-violet-500/25'],
        'meeting'      => ['bg' => 'bg-amber-500/15',   'text' => 'text-amber-300',   'border' => 'border-amber-500/25'],
        'site_visit'   => ['bg' => 'bg-blue-500/15',    'text' => 'text-blue-300',    'border' => 'border-blue-500/25'],
        'negotiation'  => ['bg' => 'bg-amber-500/15',   'text' => 'text-amber-300',   'border' => 'border-amber-500/25'],
        'reserved'     => ['bg' => 'bg-violet-500/15',  'text' => 'text-violet-300',  'border' => 'border-violet-500/25'],
        'contract'     => ['bg' => 'bg-brand-500/15',   'text' => 'text-brand-300',   'border' => 'border-brand-500/25'],
        'delivered'    => ['bg' => 'bg-emerald-500/15', 'text' => 'text-emerald-300', 'border' => 'border-emerald-500/25'],
        // Deal status
        'open'         => ['bg' => 'bg-sky-500/15',     'text' => 'text-sky-300',     'border' => 'border-sky-500/25'],
        'won'          => ['bg' => 'bg-emerald-500/15', 'text' => 'text-emerald-300', 'border' => 'border-emerald-500/25'],
        'lost'         => ['bg' => 'bg-rose-500/15',    'text' => 'text-rose-300',    'border' => 'border-rose-500/25'],
        // Task status
        'in_progress'  => ['bg' => 'bg-amber-500/15',   'text' => 'text-amber-300',   'border' => 'border-amber-500/25'],
        'completed'    => ['bg' => 'bg-emerald-500/15', 'text' => 'text-emerald-300', 'border' => 'border-emerald-500/25'],
        'cancelled'    => ['bg' => 'bg-rose-500/15',    'text' => 'text-rose-300',    'border' => 'border-rose-500/25'],
        // Offer status
        'draft'        => ['bg' => 'bg-slate-500/15',   'text' => 'text-slate-300',   'border' => 'border-slate-500/25'],
        'sent'         => ['bg' => 'bg-sky-500/15',     'text' => 'text-sky-300',     'border' => 'border-sky-500/25'],
        'accepted'     => ['bg' => 'bg-emerald-500/15', 'text' => 'text-emerald-300', 'border' => 'border-emerald-500/25'],
        'rejected'     => ['bg' => 'bg-rose-500/15',    'text' => 'text-rose-300',    'border' => 'border-rose-500/25'],
        'expired'      => ['bg' => 'bg-amber-500/15',   'text' => 'text-amber-300',   'border' => 'border-amber-500/25'],
        // Reservation status
        'pending'      => ['bg' => 'bg-amber-500/15',   'text' => 'text-amber-300',   'border' => 'border-amber-500/25'],
        'paid'         => ['bg' => 'bg-sky-500/15',     'text' => 'text-sky-300',     'border' => 'border-sky-500/25'],
        'converted'    => ['bg' => 'bg-emerald-500/15', 'text' => 'text-emerald-300', 'border' => 'border-emerald-500/25'],
        // Follow-up status
        'overdue'      => ['bg' => 'bg-rose-500/15',    'text' => 'text-rose-300',    'border' => 'border-rose-500/25'],
        // Activity status
        'done'         => ['bg' => 'bg-emerald-500/15', 'text' => 'text-emerald-300', 'border' => 'border-emerald-500/25'],
        // Priorities
        'urgent'       => ['bg' => 'bg-rose-500/15',    'text' => 'text-rose-300',    'border' => 'border-rose-500/25'],
        'high'         => ['bg' => 'bg-amber-500/15',   'text' => 'text-amber-300',   'border' => 'border-amber-500/25'],
        'medium'       => ['bg' => 'bg-sky-500/15',     'text' => 'text-sky-300',     'border' => 'border-sky-500/25'],
        'low'          => ['bg' => 'bg-slate-500/15',   'text' => 'text-slate-300',   'border' => 'border-slate-500/25'],
        'normal'       => ['bg' => 'bg-slate-500/15',   'text' => 'text-slate-300',   'border' => 'border-slate-500/25'],
        // Lead status
        'active'       => ['bg' => 'bg-emerald-500/15', 'text' => 'text-emerald-300', 'border' => 'border-emerald-500/25'],
        'inactive'     => ['bg' => 'bg-slate-500/15',   'text' => 'text-slate-300',   'border' => 'border-slate-500/25'],
    ];

    $key = strtolower((string) ($status ?? ''));
    $style = $statusMap[$key] ?? ['bg' => 'bg-slate-500/15', 'text' => 'text-slate-300', 'border' => 'border-slate-500/25'];
    $label = $label ?? __(ucfirst(str_replace('_', ' ', $key))) ?: $key;
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }}">
    <span class="h-1.5 w-1.5 rounded-full bg-current opacity-80"></span>
    {{ $label }}
</span>
