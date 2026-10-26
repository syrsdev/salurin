@props([
    'title',
    'value',
    'unit',
    'detail',
    'note',
    'tone' => 'blue',
    'icon' => 'users',
])

@php
    $tones = [
        'blue' => [
            'icon' => 'bg-blue-50 text-blue-600',
            'detail' => 'text-blue-600',
        ],
        'green' => [
            'icon' => 'bg-emerald-50 text-emerald-600',
            'detail' => 'text-emerald-700',
        ],
        'amber' => [
            'icon' => 'bg-amber-50 text-amber-600',
            'detail' => 'text-amber-700',
        ],
    ];
    $toneClasses = $tones[$tone] ?? $tones['blue'];
@endphp

<section {{ $attributes->class('rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5') }}>
    <div class="flex items-start justify-between gap-3">
        <h3 class="text-xs font-medium text-slate-500 sm:text-sm">{{ $title }}</h3>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $toneClasses['icon'] }}">
            @if ($icon === 'wallet')
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6.5A2.5 2.5 0 0 1 6.5 4H20v15H6.5A2.5 2.5 0 0 1 4 16.5v-10Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h13a2 2 0 0 1 2 2v6h-5a3 3 0 0 1 0-6h5M14 12h.01" />
                </svg>
            @elseif ($icon === 'chat')
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5h14v11H9l-4 3V5Z" />
                </svg>
            @else
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="9" cy="8" r="3" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.5 19a5.5 5.5 0 0 1 11 0M16 5.5a3 3 0 0 1 0 5.8M17 14a5 5 0 0 1 3.5 4.8" />
                </svg>
            @endif
        </span>
    </div>
    <div class="mt-2 flex items-baseline gap-2">
        <p class="text-3xl font-bold tracking-tight text-slate-900">{{ $value }}</p>
        <span class="text-[11px] text-slate-500">{{ $unit }}</span>
    </div>
    <p class="mt-1.5 text-[11px] font-medium {{ $toneClasses['detail'] }}">{{ $detail }}</p>
    <p class="mt-3 border-t border-slate-100 pt-3 text-[10px] leading-4 text-slate-500">{{ $note }}</p>
</section>
