<x-dashboard-layout>
    <x-slot name="title">Penyaluran Bantuan</x-slot>
    <x-slot name="breadcrumb">Penyaluran Bantuan</x-slot>

    <div id="penyaluran-app" data-list-url="{{ route('api.penyaluran.index') }}"
        data-api-base="{{ url('/api/penyaluran') }}" data-detail-id="{{ $penyaluranId ?? '' }}" class="space-y-5">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Ruang kerja Petugas Desa</p>
                <h1 id="page-title" class="mt-1 text-2xl font-bold text-slate-900">Daftar Penyaluran Bantuan</h1>
                <p id="page-description" class="mt-1 text-sm text-slate-500">Pantau perkembangan penyaluran bantuan di
                    wilayah tugas.</p>
            </div>
            <button type="button" id="reload-button"
                class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Muat ulang
            </button>
        </div>

        <div id="page-alert" class="hidden rounded-lg border p-3 text-sm" role="status" aria-live="polite"></div>

        <section id="list-section" class="space-y-4">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="grid gap-3 border-b border-slate-100 p-4 md:grid-cols-[1fr_220px_auto] md:items-end">
                    <div>
                        <label for="search-input" class="mb-1 block text-xs font-semibold text-slate-600">Cari nama
                            penerima atau NIK</label>
                        <input id="search-input" type="search" maxlength="100" placeholder="Masukkan nama atau NIK"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="status-filter" class="mb-1 block text-xs font-semibold text-slate-600">Status
                            penyaluran</label>
                        <select id="status-filter"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                            <option value="">Semua status</option>
                            <option value="terdaftar">Terdaftar</option>
                            <option value="diverifikasi">Diverifikasi</option>
                            <option value="dijadwalkan">Dijadwalkan</option>
                            <option value="cair">Cair</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="search-button"
                            class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Cari</button>
                        <button type="button" id="reset-button"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</button>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 px-4 py-3">
                    <p id="list-summary" class="text-xs text-slate-500">Memuat data penyaluran...</p>
                    <p id="page-counter" class="text-xs font-medium text-slate-500"></p>
                </div>
                <div id="list-container" class="grid gap-3 px-4 pb-4" aria-live="polite">
                    <div class="rounded-lg border border-slate-200 p-6 text-center text-sm text-slate-500">Memuat
                        data...</div>
                </div>
                <div
                    class="flex flex-col gap-3 border-t border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p id="pagination-info" class="text-xs text-slate-500"></p>
                    <nav id="pagination" class="flex flex-wrap items-center gap-1" aria-label="Navigasi halaman"></nav>
                </div>
            </div>
        </section>

        <section id="detail-section" class="hidden space-y-4">
            <a href="{{ route('penyaluran.page') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:text-blue-900">
                <span aria-hidden="true">←</span> Kembali ke daftar penyaluran
            </a>
            <div id="detail-container" class="space-y-4">
                <div class="rounded-xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">Memuat
                    detail...</div>
            </div>
        </section>
    </div>

    <div id="status-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/50 p-4"
        role="dialog" aria-modal="true" aria-labelledby="status-modal-title">
        <form id="status-form" class="w-full max-w-lg rounded-xl bg-white shadow-xl">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 id="status-modal-title" class="text-lg font-bold text-slate-900">Ubah Status Penyaluran</h2>
                <p class="mt-1 text-sm text-slate-500">Perbarui tahap penyaluran bantuan penerima.</p>
            </div>
            <div class="space-y-4 p-5">
                <div>
                    <label for="status-value" class="mb-1 block text-sm font-semibold text-slate-700">Status
                        penyaluran</label>
                    <select id="status-value" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                        <option value="terdaftar">Terdaftar</option>
                        <option value="diverifikasi">Diverifikasi</option>
                        <option value="dijadwalkan">Dijadwalkan</option>
                        <option value="cair">Cair</option>
                    </select>
                </div>
                <div>
                    <label for="date-value" class="mb-1 block text-sm font-semibold text-slate-700">Tanggal penyaluran
                        (opsional)</label>
                    <input id="date-value" type="date"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                </div>
                <div>
                    <label for="note-value" class="mb-1 block text-sm font-semibold text-slate-700">Catatan
                        (opsional)</label>
                    <textarea id="note-value" rows="3" maxlength="1000" placeholder="Tambahkan catatan perubahan status..."
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600"></textarea>
                </div>
                <p id="form-error" class="hidden text-sm text-red-700" role="alert"></p>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
                <button type="button" id="cancel-status"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" id="save-status"
                    class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Simpan
                    perubahan</button>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const root = document.getElementById('penyaluran-app');
            if (!root) return;

            const listUrl = root.dataset.listUrl;
            const apiBase = root.dataset.apiBase;
            const detailId = root.dataset.detailId;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

            const $ = id => document.getElementById(id);
            const statuses = [{
                    key: 'terdaftar',
                    label: 'Terdaftar',
                    description: 'Penerima masuk dalam daftar penyaluran.'
                },
                {
                    key: 'diverifikasi',
                    label: 'Diverifikasi',
                    description: 'Data penerima dan bantuan telah diverifikasi.'
                },
                {
                    key: 'dijadwalkan',
                    label: 'Dijadwalkan',
                    description: 'Penyaluran bantuan telah dijadwalkan.'
                },
                {
                    key: 'cair',
                    label: 'Cair',
                    description: 'Bantuan telah disalurkan kepada penerima.'
                }
            ];

            let page = 1;
            let search = '';
            let status = '';
            let activeRecord = null;

            const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            } [char]));
            const labelFor = key => statuses.find(item => item.key === key)?.label || key || 'Belum diketahui';
            const badgeFor = key => ({
                terdaftar: 'bg-slate-100 text-slate-700',
                diverifikasi: 'bg-blue-50 text-blue-700',
                dijadwalkan: 'bg-amber-50 text-amber-700',
                cair: 'bg-emerald-50 text-emerald-700'
            } [key] || 'bg-slate-100 text-slate-700');
            const maskNik = value => {
                const nik = String(value || '');
                return nik.length > 4 ? `${'*'.repeat(nik.length - 4)}${nik.slice(-4)}` : (nik || '—');
            };
            const formatDate = value => {
                if (!value) return '—';
                const date = new Date(`${String(value).slice(0, 10)}T00:00:00`);
                return Number.isNaN(date.getTime()) ? String(value) :
                    new Intl.DateTimeFormat('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    }).format(date);
            };

            function alertMessage(message, isError = false) {
                $('page-alert').textContent = message;
                $('page-alert').className = `rounded-lg border p-3 text-sm ${isError
                    ? 'border-red-200 bg-red-50 text-red-800'
                    : 'border-emerald-200 bg-emerald-50 text-emerald-800'}`;
                $('page-alert').classList.remove('hidden');
            }

            function clearAlert() {
                $('page-alert').classList.add('hidden');
            }

            async function api(url, options = {}) {
                const method = (options.method || 'GET').toUpperCase();
                const mutating = ['POST', 'PATCH', 'PUT', 'DELETE'].includes(method);
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    ...options,
                    headers: {
                        'Accept': 'application/json',
                        ...(mutating ? {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest'
                        } : {}),
                        ...(options.headers || {})
                    }
                });
                const contentType = response.headers.get('content-type') || '';
                let body = {};
                if (contentType.includes('application/json')) body = await response.json();
                else if (!response.ok) body.message = `Server memberikan respons HTTP ${response.status}.`;
                if (!response.ok) {
                    const validation = body.errors ? Object.values(body.errors).flat().join(' ') : '';
                    throw new Error(validation || body.message || `Permintaan gagal (HTTP ${response.status}).`);
                }
                return body;
            }

            function button(text, className, onClick, disabled = false) {
                const el = document.createElement('button');
                el.type = 'button';
                el.textContent = text;
                el.className = className;
                el.disabled = disabled;
                el.addEventListener('click', onClick);
                return el;
            }

            function renderPagination(meta) {
                const nav = $('pagination');
                nav.replaceChildren();
                const current = Number(meta.current_page || 1);
                const last = Number(meta.last_page || 1);
                const common =
                    'rounded-lg border px-3 py-2 text-xs font-semibold disabled:cursor-not-allowed disabled:opacity-40';
                nav.append(button('Sebelumnya', `${common} border-slate-300 text-slate-600 hover:bg-slate-50`,
                    () => loadList(current - 1), current <= 1));

                const pages = last <= 5 ? Array.from({
                    length: last
                }, (_, i) => i + 1) : [...new Set([1, current - 1, current, current + 1, last])].filter(n => n >=
                    1 && n <= last).sort((a,
                    b) => a - b);
                let previous = 0;
                pages.forEach(n => {
                    if (previous && n - previous > 1) {
                        const dots = document.createElement('span');
                        dots.className = 'px-1 text-slate-400';
                        dots.textContent = '…';
                        nav.append(dots);
                    }
                    nav.append(button(String(n),
                        `${common} ${n === current ? 'border-blue-700 bg-blue-700 text-white' : 'border-slate-300 text-slate-600 hover:bg-slate-50'}`,
                        () => loadList(n), n === current));
                    previous = n;
                });
                nav.append(button('Berikutnya', `${common} border-slate-300 text-slate-600 hover:bg-slate-50`,
                    () => loadList(current + 1), current >= last));

                $('page-counter').textContent = `Halaman ${current} dari ${last}`;
                $('pagination-info').textContent =
                    `Menampilkan ${meta.from || 0}–${meta.to || 0} dari ${meta.total || 0} penyaluran`;
            }

            function renderList(items) {
                const container = $('list-container');
                container.replaceChildren();
                if (!items.length) {
                    const empty = document.createElement('div');
                    empty.className =
                        'rounded-lg border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500';
                    empty.textContent = 'Belum ada data penyaluran yang sesuai.';
                    container.append(empty);
                    return;
                }

                items.forEach(item => {
                    const recipient = item.penerima || {};
                    const card = document.createElement('article');
                    card.className =
                        'rounded-xl border border-slate-200 bg-white p-4 transition hover:border-blue-200 hover:shadow-sm sm:p-5';
                    card.innerHTML = `
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-base font-bold text-slate-900">${escapeHtml(recipient.nama_lengkap || 'Nama penerima tidak tersedia')}</h2>
                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold ${badgeFor(item.status_penyaluran)}">${escapeHtml(labelFor(item.status_penyaluran))}</span>
                                </div>
                                <div class="mt-3 grid gap-3 text-sm sm:grid-cols-3">
                                    <div><p class="text-xs text-slate-500">NIK</p><p class="mt-1 font-mono text-slate-700">${escapeHtml(maskNik(recipient.nik))}</p></div>
                                    <div><p class="text-xs text-slate-500">ID penyaluran</p><p class="mt-1 text-slate-700">#${Number(item.id)}</p></div>
                                    <div><p class="text-xs text-slate-500">Tanggal penyaluran</p><p class="mt-1 text-slate-700">${escapeHtml(formatDate(item.tgl_penyaluran))}</p></div>
                                </div>
                            </div>
                            <a href="/dashboard/penyaluran/${Number(item.id)}"
                                class="inline-flex shrink-0 items-center justify-center rounded-lg bg-blue-700 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-800">Lihat detail</a>
                        </div>`;
                    container.append(card);
                });
            }

            async function loadList(nextPage = 1) {
                page = Math.max(1, nextPage);
                clearAlert();
                $('list-container').innerHTML =
                    '<div class="rounded-lg border border-slate-200 p-6 text-center text-sm text-slate-500">Memuat data...</div>';
                const params = new URLSearchParams({
                    page: String(page)
                });
                if (search) params.set('search', search);
                if (status) params.set('status', status);
                try {
                    const result = await api(`${listUrl}?${params.toString()}`);
                    renderList(result.data || []);
                    renderPagination(result);
                    $('list-summary').textContent = `${result.total || 0} penyaluran ditemukan`;
                } catch (error) {
                    $('list-container').replaceChildren();
                    const msg = document.createElement('p');
                    msg.className = 'rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800';
                    msg.textContent = error.message;
                    $('list-container').append(msg);
                    $('list-summary').textContent = 'Data gagal dimuat';
                    $('pagination').replaceChildren();
                    alertMessage(error.message, true);
                }
            }

            function timeline(statusKey, dateValue, history = [], createdAt = null) {
                const dates = {
                    terdaftar: createdAt,
                    diverifikasi: null,
                    dijadwalkan: null,
                    cair: null
                };

                history.forEach(item => {
                    if (item.status_baru && dates[item.status_baru] !== undefined) {
                        dates[item.status_baru] = item.waktu;
                    }
                });

                const activeIndex = statuses.findIndex(
                    item => item.key === statusKey
                );

                return `
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-slate-900">
                Garis Waktu Penyaluran
            </h2>

            <ol class="mt-6 grid gap-4 sm:grid-cols-4">
                ${statuses.map((step, index) => {
                    const done = activeIndex >= index;
                    const date = dates[step.key];

                    return `
                            <li class="flex gap-3 sm:flex-col">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                    ${done ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-400'}">
                                    ${done ? '✓' : index + 1}
                                </span>

                                <div>
                                    <p class="text-sm font-semibold text-slate-900">
                                        ${escapeHtml(step.label)}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        ${date ? escapeHtml(formatDate(String(date).slice(0, 10))) : 'Belum tercatat'}
                                    </p>
                                </div>
                            </li>
                        `;
                }).join('')}
            </ol>
        </section>
    `;
            }

            async function loadDetail(id) {
                clearAlert();
                $('detail-container').innerHTML =
                    '<div class="rounded-xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">Memuat detail...</div>';
                try {
                    const result = await api(`${apiBase}/${encodeURIComponent(id)}`);
                    activeRecord = result.data;
                    const historyResult = await api(`${apiBase}/${encodeURIComponent(id)}/riwayat`);
                    const history = historyResult.data || [];
                    const item = activeRecord;
                    const recipient = item.penerima || {};
                    $('page-title').textContent = `Detail Penyaluran #${item.id}`;
                    $('page-description').textContent = 'Informasi penerima dan perkembangan penyaluran bantuan.';
                    $('detail-container').innerHTML = `
                        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div><p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Informasi penerima</p>
                                <h2 class="mt-1 text-xl font-bold text-slate-900">${escapeHtml(recipient.nama_lengkap || 'Nama tidak tersedia')}</h2>
                                <p class="mt-1 font-mono text-sm text-slate-500">NIK ${escapeHtml(maskNik(recipient.nik))}</p></div>
                                <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold ${badgeFor(item.status_penyaluran)}">${escapeHtml(labelFor(item.status_penyaluran))}</span>
                            </div>
                            <dl class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <div><dt class="text-xs text-slate-500">Alamat</dt><dd class="mt-1 text-sm text-slate-800">${escapeHtml(recipient.alamat || 'Belum tersedia di response API')}</dd></div>
                                <div><dt class="text-xs text-slate-500">Jenis bantuan</dt><dd class="mt-1 text-sm text-slate-800">${escapeHtml(item.jenis_bantuan?.nama_bantuan || `ID bantuan #${item.id_bantuan ?? '—'}`)}</dd></div>
                                <div><dt class="text-xs text-slate-500">Periode</dt><dd class="mt-1 text-sm text-slate-800">${escapeHtml(item.periode?.nama_periode || `ID periode #${item.id_periode ?? '—'}`)}</dd></div>
                                <div><dt class="text-xs text-slate-500">Tanggal penyaluran</dt><dd class="mt-1 text-sm text-slate-800">${escapeHtml(formatDate(item.tgl_penyaluran))}</dd></div>
                                <div class="sm:col-span-2"><dt class="text-xs text-slate-500">Catatan</dt><dd class="mt-1 whitespace-pre-line text-sm text-slate-800">${escapeHtml(item.catatan || 'Belum ada catatan.')}</dd></div>
                            </dl>
                            <div class="mt-5 flex flex-wrap gap-2">
                                <button type="button" id="edit-status-button" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Ubah status</button>
                                <a href="{{ route('penyaluran.page') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke daftar</a>
                            </div>
                        </section>
                        ${timeline(item.status_penyaluran, item.tgl_penyaluran, history, item.created_at)}`;
                    $('edit-status-button').addEventListener('click', openModal);
                } catch (error) {
                    $('detail-container').textContent = error.message;
                    alertMessage(error.message, true);
                }
            }

            function openModal() {
                if (!activeRecord) return;
                $('form-error').classList.add('hidden');
                $('status-value').value = activeRecord.status_penyaluran || 'terdaftar';
                $('date-value').value = activeRecord.tgl_penyaluran || '';
                $('note-value').value = activeRecord.catatan || '';
                $('status-modal').classList.remove('hidden');
                $('status-modal').classList.add('flex');
            }

            function closeModal() {
                $('status-modal').classList.add('hidden');
                $('status-modal').classList.remove('flex');
            }

            $('status-form').addEventListener('submit', async event => {
                event.preventDefault();
                if (!activeRecord) return;
                const save = $('save-status');
                save.disabled = true;
                save.textContent = 'Menyimpan...';
                $('form-error').classList.add('hidden');
                const payload = {
                    status_penyaluran: $('status-value').value,
                    tgl_penyaluran: $('date-value').value || null,
                    catatan: $('note-value').value.trim() || null
                };
                try {
                    const result = await api(`${apiBase}/${activeRecord.id}/status`, {
                        method: 'PATCH',
                        body: JSON.stringify(payload)
                    });
                    closeModal();
                    alertMessage(result.message || 'Status penyaluran berhasil diperbarui.');
                    await loadDetail(activeRecord.id);
                } catch (error) {
                    $('form-error').textContent = error.message;
                    $('form-error').classList.remove('hidden');
                } finally {
                    save.disabled = false;
                    save.textContent = 'Simpan perubahan';
                }
            });

            $('cancel-status').addEventListener('click', closeModal);
            $('status-modal').addEventListener('click', event => {
                if (event.target === $('status-modal')) closeModal();
            });
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') closeModal();
            });
            $('reload-button').addEventListener('click', () => detailId ? loadDetail(detailId) : loadList(page));
            $('search-button').addEventListener('click', () => {
                search = $('search-input').value.trim();
                status = $('status-filter').value;
                loadList(1);
            });
            $('search-input').addEventListener('keydown', event => {
                if (event.key === 'Enter') {
                    search = event.target.value.trim();
                    status = $('status-filter').value;
                    loadList(1);
                }
            });
            $('status-filter').addEventListener('change', event => {
                status = event.target.value;
                loadList(1);
            });
            $('reset-button').addEventListener('click', () => {
                $('search-input').value = '';
                $('status-filter').value = '';
                search = '';
                status = '';
                loadList(1);
            });

            if (detailId) {
                $('list-section').classList.add('hidden');
                $('detail-section').classList.remove('hidden');
                loadDetail(detailId);
            } else {
                loadList(1);
            }
        })();
    </script>
</x-dashboard-layout>
