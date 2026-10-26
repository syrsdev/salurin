@props(['status'])

@php
    $statusClasses = [
        'Menunggu' => 'bg-amber-50 text-amber-700 before:bg-amber-600',
        'Selesai' => 'bg-emerald-50 text-emerald-700 before:bg-emerald-600',
        'Disetujui' => 'bg-emerald-50 text-emerald-700 before:bg-emerald-600',
        'Diproses' => 'bg-orange-50 text-orange-700 before:bg-orange-600',
        'Ditolak' => 'bg-red-50 text-red-700 before:bg-red-600',
    ];
    $classes = $statusClasses[$status] ?? 'bg-slate-100 text-slate-700 before:bg-slate-500';
@endphp

<span {{ $attributes->class("inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2 py-1 text-[10px] font-semibold before:h-1.5 before:w-1.5 before:rounded-full before:content-[''] {$classes}") }}>
    {{ $status }}
</span>
