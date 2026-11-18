@props([
    'activities',
    'title' => 'Aktivitas terbaru',
    'description' => 'Perubahan terbaru di wilayah operasional.',
])

<section {{ $attributes->class('overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm') }} aria-labelledby="recent-activity-title">
    <div class="flex flex-col justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:px-5">
        <div>
            <h2 id="recent-activity-title" class="text-sm font-bold text-slate-900">{{ $title }}</h2>
            <p class="mt-1 text-[10px] text-slate-500">{{ $description }}</p>
        </div>
        <p class="text-[10px] text-slate-500">Urutan terbaru terlebih dahulu</p>
    </div>

    @if ($activities->isEmpty())
        <p class="px-5 py-8 text-center text-xs text-slate-500">Belum ada aktivitas yang tercatat.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] border-collapse text-left">
                <thead class="bg-slate-50">
                    <tr class="text-[10px] font-semibold text-slate-500">
                        <th scope="col" class="px-4 py-3 sm:px-5">Waktu</th>
                        <th scope="col" class="px-4 py-3">Aktivitas / rincian</th>
                        <th scope="col" class="px-4 py-3">Wilayah</th>
                        <th scope="col" class="px-4 py-3">Pelaku aksi</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($activities as $activity)
                        @php($activityTime = $activity['waktu']->copy()->locale('id'))
                        <tr class="text-[10px] even:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 align-top sm:px-5">
                                <p class="font-semibold text-slate-800">{{ $activityTime->format('H.i') }} WIB</p>
                                <p class="mt-1 text-[9px] text-slate-400">{{ $activityTime->translatedFormat('d M Y') }}</p>
                            </td>
                            <td class="min-w-64 px-4 py-3">
                                <p class="font-semibold text-slate-800">{{ $activity['title'] }}</p>
                                <p class="mt-1 text-[9px] text-slate-500">{{ $activity['detail'] }}</p>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700">{{ $activity['area'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $activity['actor'] }}</td>
                            <td class="px-4 py-3"><x-dashboard.status-badge :status="$activity['status']" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="border-t border-slate-200 px-4 py-3 text-[9px] text-slate-500 sm:px-5">
        {{ $activities->count() }} aktivitas ditampilkan · Sumber: catatan aplikasi
    </div>
</section>
