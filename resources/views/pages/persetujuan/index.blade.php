<x-dashboard-layout>
    <x-slot name="title">Persetujuan Data Penerima</x-slot>
    <x-slot name="breadcrumb">Persetujuan Data Penerima</x-slot>

    <div id="approval-app"
        data-list-url="{{ route('api.persetujuan.index') }}"
        data-base-url="{{ url('/api/persetujuan-penerima') }}"
        class="space-y-5">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Ruang kerja Kepala Desa</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">Persetujuan Data Penerima</h1>
                <p class="mt-1 text-sm text-slate-500">Periksa pengajuan penerima bantuan sebelum memberikan keputusan.</p>
            </div>
            <button type="button" id="refresh-approvals"
                class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Muat ulang
            </button>
        </div>

        <div id="approval-alert" class="hidden rounded-lg border p-3 text-sm" role="status" aria-live="polite"></div>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="approval-search" class="mb-1 block text-xs font-semibold text-slate-600">Cari nama atau NIK</label>
                    <input id="approval-search" type="search" maxlength="100" placeholder="Masukkan nama atau NIK..."
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600">
                </div>
                <div class="flex gap-2">
                    <button type="button" id="approval-search-button"
                        class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Cari</button>
                    <button type="button" id="approval-reset"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</button>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 border-b border-slate-100 px-4 py-3" aria-label="Filter status pengajuan">
                <button type="button" data-status="" aria-pressed="true"
                    class="approval-tab rounded-lg bg-blue-700 px-3 py-2 text-xs font-semibold text-white">Semua</button>
                <button type="button" data-status="menunggu" aria-pressed="false"
                    class="approval-tab rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">Menunggu</button>
                <button type="button" data-status="disetujui" aria-pressed="false"
                    class="approval-tab rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">Disetujui</button>
                <button type="button" data-status="ditolak" aria-pressed="false"
                    class="approval-tab rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">Ditolak</button>
            </div>

            <div class="flex items-center justify-between gap-3 px-4 py-3">
                <p id="approval-summary" class="text-xs text-slate-500">Memuat pengajuan...</p>
                <p id="approval-page-label" class="text-xs font-medium text-slate-500"></p>
            </div>

            <div id="approval-list" class="grid gap-3 px-4 pb-4" aria-live="polite">
                <div class="rounded-lg border border-slate-200 p-6 text-center text-sm text-slate-500">Memuat data pengajuan...</div>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                <p id="approval-pagination-info" class="text-xs text-slate-500"></p>
                <nav id="approval-pagination" class="flex flex-wrap items-center gap-1" aria-label="Navigasi halaman"></nav>
            </div>
        </section>
    </div>

    <div id="approval-detail-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-950/50 p-4"
        role="dialog" aria-modal="true" aria-labelledby="approval-detail-title">
        <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 id="approval-detail-title" class="text-lg font-bold text-slate-900">Detail Pengajuan</h2>
                <button type="button" class="close-approval-modal rounded-lg px-3 py-1 text-slate-500 hover:bg-slate-100" aria-label="Tutup">✕</button>
            </div>
            <div id="approval-detail-content" class="p-5"></div>
            <div class="flex justify-end border-t border-slate-100 px-5 py-4">
                <button type="button" class="close-approval-modal rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Tutup</button>
            </div>
        </div>
    </div>

    <div id="approval-reject-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/50 p-4"
        role="dialog" aria-modal="true" aria-labelledby="reject-title">
        <form id="approval-reject-form" class="w-full max-w-lg rounded-xl bg-white shadow-xl">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 id="reject-title" class="text-lg font-bold text-slate-900">Tolak Pengajuan</h2>
                <p class="mt-1 text-sm text-slate-500">Alasan penolakan wajib diisi minimal 10 karakter.</p>
            </div>
            <div class="p-5">
                <label for="reject-reason" class="mb-1 block text-sm font-semibold text-slate-700">Alasan penolakan</label>
                <textarea id="reject-reason" name="alasan_penolakan" rows="4" minlength="10" maxlength="1000" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-600 focus:ring-blue-600"
                    placeholder="Contoh: Dokumen pendukung belum lengkap..."></textarea>
                <p id="reject-error" class="mt-2 hidden text-xs text-red-600"></p>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
                <button type="button" id="cancel-reject"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" id="submit-reject"
                    class="rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">Tolak Pengajuan</button>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const root = document.getElementById('approval-app');
            if (!root) return;

            const listUrl = root.dataset.listUrl;
            const baseUrl = root.dataset.baseUrl;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const listEl = document.getElementById('approval-list');
            const alertEl = document.getElementById('approval-alert');
            const summaryEl = document.getElementById('approval-summary');
            const pageLabelEl = document.getElementById('approval-page-label');
            const paginationInfoEl = document.getElementById('approval-pagination-info');
            const paginationEl = document.getElementById('approval-pagination');
            const detailModal = document.getElementById('approval-detail-modal');
            const detailContent = document.getElementById('approval-detail-content');
            const rejectModal = document.getElementById('approval-reject-modal');
            const rejectForm = document.getElementById('approval-reject-form');
            const rejectReason = document.getElementById('reject-reason');
            const rejectError = document.getElementById('reject-error');

            let currentPage = 1;
            let currentStatus = '';
            let currentSearch = '';
            let rejectingId = null;

            const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            }[char]));

            const maskNik = nik => {
                const value = String(nik ?? '');
                return value.length > 4 ? `${'*'.repeat(value.length - 4)}${value.slice(-4)}` : value;
            };

            const statusClass = status => ({
                menunggu: 'bg-amber-50 text-amber-700',
                disetujui: 'bg-emerald-50 text-emerald-700',
                ditolak: 'bg-red-50 text-red-700'
            }[status] || 'bg-slate-100 text-slate-700');

            const showAlert = (message, type = 'success') => {
                alertEl.textContent = message;
                alertEl.className = `rounded-lg border p-3 text-sm ${type === 'error'
                    ? 'border-red-200 bg-red-50 text-red-800'
                    : 'border-emerald-200 bg-emerald-50 text-emerald-800'}`;
                alertEl.classList.remove('hidden');
            };

            const hideAlert = () => alertEl.classList.add('hidden');

            // API memakai session login Laravel. GET mengirim Accept JSON;
            // PATCH juga mengirim CSRF token sesuai instruksi backend.
            async function apiFetch(url, options = {}) {
                const isMutation = ['PATCH', 'POST', 'PUT', 'DELETE'].includes(options.method);
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    ...options,
                    headers: {
                        'Accept': 'application/json',
                        ...(isMutation ? {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        } : {}),
                        ...(options.headers || {})
                    }
                });

                const contentType = response.headers.get('content-type') || '';
                let body = {};
                if (contentType.includes('application/json')) {
                    body = await response.json();
                } else if (!response.ok) {
                    body.message = `Server memberi respons HTTP ${response.status}. Periksa login, middleware, dan log Laravel.`;
                }

                if (!response.ok) {
                    const validation = body.errors ? Object.values(body.errors).flat().join(' ') : '';
                    throw new Error(validation || body.message || `Permintaan gagal (HTTP ${response.status}).`);
                }
                return body;
            }

            function makeButton(label, className, onClick, disabled = false) {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.className = className;
                button.disabled = disabled;
                button.addEventListener('click', onClick);
                return button;
            }

            function renderPagination(meta) {
                paginationEl.replaceChildren();
                const page = Number(meta.current_page || 1);
                const last = Number(meta.last_page || 1);
                const baseClass = 'rounded-lg border px-3 py-2 text-xs font-semibold disabled:cursor-not-allowed disabled:opacity-40';

                paginationEl.append(makeButton('Sebelumnya',
                    `${baseClass} border-slate-300 text-slate-600 hover:bg-slate-50`,
                    () => load(page - 1), page <= 1));

                // Tampilkan nomor halaman terdekat dan halaman awal/akhir bila jumlahnya banyak.
                let pages = [];
                if (last <= 5) {
                    pages = Array.from({ length: last }, (_, i) => i + 1);
                } else {
                    pages = [...new Set([1, page - 1, page, page + 1, last])].filter(n => n >= 1 && n <= last).sort((a, b) => a - b);
                }

                let previous = 0;
                pages.forEach(number => {
                    if (previous && number - previous > 1) {
                        const dots = document.createElement('span');
                        dots.className = 'px-1 text-slate-400';
                        dots.textContent = '…';
                        paginationEl.append(dots);
                    }
                    paginationEl.append(makeButton(String(number),
                        `${baseClass} ${number === page ? 'border-blue-700 bg-blue-700 text-white' : 'border-slate-300 text-slate-600 hover:bg-slate-50'}`,
                        () => load(number), number === page));
                    previous = number;
                });

                paginationEl.append(makeButton('Berikutnya',
                    `${baseClass} border-slate-300 text-slate-600 hover:bg-slate-50`,
                    () => load(page + 1), page >= last));

                pageLabelEl.textContent = `Halaman ${page} dari ${last}`;
                paginationInfoEl.textContent = `Menampilkan ${meta.from || 0}–${meta.to || 0} dari ${meta.total || 0} pengajuan`;
            }

            function renderCards(items) {
                listEl.replaceChildren();
                if (!items.length) {
                    const empty = document.createElement('div');
                    empty.className = 'rounded-lg border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500';
                    empty.textContent = 'Belum ada pengajuan yang sesuai dengan pencarian atau filter.';
                    listEl.append(empty);
                    return;
                }

                items.forEach(item => {
                    const status = String(item.status_data || 'menunggu');
                    const label = status.charAt(0).toUpperCase() + status.slice(1);
                    const card = document.createElement('article');
                    card.className = 'rounded-xl border border-slate-200 bg-white p-4 transition hover:border-blue-200 hover:shadow-sm sm:p-5';
                    card.innerHTML = `
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-base font-bold text-slate-900">${escapeHtml(item.nama_lengkap)}</h2>
                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold ${statusClass(status)}">${escapeHtml(label)}</span>
                                </div>
                                <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
                                    <div><dt class="text-xs text-slate-500">NIK</dt><dd class="mt-1 font-mono text-slate-700">${escapeHtml(maskNik(item.nik))}</dd></div>
                                    <div><dt class="text-xs text-slate-500">Tanggal lahir</dt><dd class="mt-1 text-slate-700">${escapeHtml(item.tanggal_lahir || '—')}</dd></div>
                                    <div><dt class="text-xs text-slate-500">Alamat</dt><dd class="mt-1 text-slate-700">${escapeHtml(item.alamat || '—')}</dd></div>
                                </dl>
                                ${status === 'ditolak' && item.alasan_penolakan
                                    ? `<p class="mt-3 rounded-lg bg-red-50 p-3 text-xs text-red-800"><strong>Alasan penolakan:</strong> ${escapeHtml(item.alasan_penolakan)}</p>`
                                    : ''}
                            </div>
                            <div class="flex shrink-0 flex-wrap gap-2 sm:justify-end">
                                <button type="button" data-action="detail" data-id="${Number(item.id)}"
                                    class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</button>
                                ${status === 'menunggu' ? `
                                    <button type="button" data-action="approve" data-id="${Number(item.id)}"
                                        class="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-800">Setujui</button>
                                    <button type="button" data-action="reject" data-id="${Number(item.id)}"
                                        class="rounded-lg bg-red-700 px-3 py-2 text-xs font-semibold text-white hover:bg-red-800">Tolak</button>
                                ` : ''}
                            </div>
                        </div>`;
                    listEl.append(card);
                });
            }

            async function load(page = 1) {
                currentPage = Math.max(1, page);
                hideAlert();
                listEl.innerHTML = '<div class="rounded-lg border border-slate-200 p-6 text-center text-sm text-slate-500">Memuat pengajuan...</div>';

                const params = new URLSearchParams({ page: String(currentPage) });
                if (currentSearch) params.set('search', currentSearch);
                if (currentStatus) params.set('status', currentStatus);

                try {
                    const result = await apiFetch(`${listUrl}?${params.toString()}`);
                    renderCards(result.data || []);
                    renderPagination(result);
                    summaryEl.textContent = `${result.total || 0} pengajuan ditemukan`;
                } catch (error) {
                    listEl.replaceChildren();
                    const message = document.createElement('p');
                    message.className = 'rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800';
                    message.textContent = error.message;
                    listEl.append(message);
                    summaryEl.textContent = 'Data gagal dimuat';
                    paginationEl.replaceChildren();
                    showAlert(error.message, 'error');
                }
            }

            async function showDetail(id) {
                try {
                    const result = await apiFetch(`${baseUrl}/${id}`);
                    const item = result.data;
                    const fields = [
                        ['Nama lengkap', item.nama_lengkap],
                        ['NIK', item.nik],
                        ['Nomor KK', item.no_kk],
                        ['Tanggal lahir', item.tanggal_lahir],
                        ['Alamat', item.alamat],
                        ['Status data', item.status_data],
                        ['Alasan penolakan', item.alasan_penolakan]
                    ];
                    detailContent.replaceChildren();
                    const dl = document.createElement('dl');
                    dl.className = 'grid gap-4 sm:grid-cols-2';
                    fields.forEach(([label, value]) => {
                        const wrapper = document.createElement('div');
                        const dt = document.createElement('dt');
                        dt.className = 'text-xs font-semibold text-slate-500';
                        dt.textContent = label;
                        const dd = document.createElement('dd');
                        dd.className = 'mt-1 break-words text-sm text-slate-800';
                        dd.textContent = value || '—';
                        wrapper.append(dt, dd);
                        dl.append(wrapper);
                    });
                    detailContent.append(dl);
                    detailModal.classList.remove('hidden');
                    detailModal.classList.add('flex');
                } catch (error) {
                    showAlert(error.message, 'error');
                }
            }

            async function approve(id) {
                if (!window.confirm('Setujui pengajuan penerima ini?')) return;
                try {
                    const result = await apiFetch(`${baseUrl}/${id}/setujui`, {
                        method: 'PATCH',
                        body: JSON.stringify({})
                    });
                    showAlert(result.message || 'Data penerima berhasil disetujui.');
                    await load(currentPage);
                } catch (error) {
                    showAlert(error.message, 'error');
                }
            }

            function openReject(id) {
                rejectingId = id;
                rejectForm.reset();
                rejectError.classList.add('hidden');
                rejectModal.classList.remove('hidden');
                rejectModal.classList.add('flex');
                rejectReason.focus();
            }

            function closeReject() {
                rejectingId = null;
                rejectModal.classList.add('hidden');
                rejectModal.classList.remove('flex');
            }

            rejectForm.addEventListener('submit', async event => {
                event.preventDefault();
                const reason = rejectReason.value.trim();
                if (reason.length < 10) {
                    rejectError.textContent = 'Alasan penolakan minimal 10 karakter.';
                    rejectError.classList.remove('hidden');
                    return;
                }

                const submitButton = document.getElementById('submit-reject');
                submitButton.disabled = true;
                submitButton.textContent = 'Mengirim...';

                try {
                    const result = await apiFetch(`${baseUrl}/${rejectingId}/tolak`, {
                        method: 'PATCH',
                        body: JSON.stringify({ alasan_penolakan: reason })
                    });
                    closeReject();
                    showAlert(result.message || 'Data penerima berhasil ditolak.');
                    await load(currentPage);
                } catch (error) {
                    rejectError.textContent = error.message;
                    rejectError.classList.remove('hidden');
                } finally {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Tolak Pengajuan';
                }
            });

            listEl.addEventListener('click', event => {
                const button = event.target.closest('button[data-action]');
                if (!button) return;
                const id = Number(button.dataset.id);
                if (button.dataset.action === 'detail') showDetail(id);
                if (button.dataset.action === 'approve') approve(id);
                if (button.dataset.action === 'reject') openReject(id);
            });

            document.querySelectorAll('.close-approval-modal').forEach(button => {
                button.addEventListener('click', () => {
                    detailModal.classList.add('hidden');
                    detailModal.classList.remove('flex');
                });
            });
            document.getElementById('cancel-reject').addEventListener('click', closeReject);
            document.getElementById('refresh-approvals').addEventListener('click', () => load(currentPage));

            document.getElementById('approval-search-button').addEventListener('click', () => {
                currentSearch = document.getElementById('approval-search').value.trim();
                load(1);
            });
            document.getElementById('approval-search').addEventListener('keydown', event => {
                if (event.key === 'Enter') {
                    currentSearch = event.target.value.trim();
                    load(1);
                }
            });
            document.getElementById('approval-reset').addEventListener('click', () => {
                document.getElementById('approval-search').value = '';
                currentSearch = '';
                currentStatus = '';
                document.querySelectorAll('.approval-tab').forEach(tab => setActiveTab(tab, tab.dataset.status === ''));
                load(1);
            });

            function setActiveTab(tab, active) {
                tab.className = `approval-tab rounded-lg px-3 py-2 text-xs font-semibold ${active
                    ? 'bg-blue-700 text-white'
                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}`;
                tab.setAttribute('aria-pressed', active ? 'true' : 'false');
            }

            document.querySelectorAll('.approval-tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    currentStatus = tab.dataset.status;
                    document.querySelectorAll('.approval-tab').forEach(item => setActiveTab(item, item === tab));
                    load(1);
                });
            });

            [detailModal, rejectModal].forEach(modal => {
                modal.addEventListener('click', event => {
                    if (event.target === modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        if (modal === rejectModal) rejectingId = null;
                    }
                });
            });

            load(1);
        })();
    </script>
</x-dashboard-layout>
