@extends('layout.main')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Form Retur Pembelian</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('retur_pembelian.index') }}">Retur Pembelian</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <form id="returPembelianForm" method="POST" action="{{ route('retur_pembelian.store') }}" class="p-3 space-y-3">
                    <div id="laravel-errors"
                        @if(session('error'))
                        data-error="{{ session('error') }}"
                        @endif
                        @if($errors->any())
                        data-validation-errors="{{ json_encode($errors->all()) }}"
                        @endif
                        ></div>
                    @csrf
                    <input type="hidden" id="form_submitted" name="form_submitted" value="0">
                    <input type="hidden" id="url_receive_items" value="{{ route('retur_pembelian.receive_items') }}">
                    <input type="hidden" id="url_purchase_invoices" value="{{ route('retur_pembelian.invoices') }}">
                    <input type="hidden" id="url_referensi_detail" value="{{ route('retur_pembelian.referensi_detail') }}">
                    <input type="hidden" id="vendor_no_hidden" name="vendor" value="">
                    <input type="hidden" id="faktur_pembelian_id" name="faktur_pembelian_id" value="">
                    <input type="hidden" id="penerimaan_barang_id" name="penerimaan_barang_id" value="">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="grid grid-cols-[150px_1fr] gap-y-4 gap-x-4 text-sm items-start">
                                <!-- Vendor -->
                                <label for="vendor_search" class="text-gray-800 font-medium flex items-center">
                                    Vendor <span class="text-red-600 ml-1">*</span>
                                    <span class="ml-1"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Silakan cari & pilih vendor dari dropdown"
                                        style="cursor: help;">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                </label>

                                <div class="relative max-w-md w-full">
                                    <div class="flex items-center border border-gray-300 rounded overflow-hidden max-w-[300px]">
                                        <input
                                            id="vendor_search"
                                            type="search"
                                            placeholder="Cari/Pilih Vendor..."
                                            class="flex-grow px-2 py-1 outline-none text-sm"
                                            required />
                                        <button type="button" id="vendor-search-btn" class="px-2 text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <div id="dropdown-vendor" class="absolute left-0 right-0 z-10 bg-white border border-gray-300 rounded shadow mt-1 hidden max-h-40 overflow-y-auto text-sm max-w-[300px]">
                                    </div>
                                    <small id="vendor-fetch-status" class="text-gray-500 hidden"></small>
                                </div>

                                <!-- Tanggal -->
                                <label for="tanggal_retur" class="text-gray-800 font-medium flex items-center">
                                    Tanggal<span class="text-red-600 ml-1">*</span>
                                </label>
                                <input id="tanggal_retur" name="tanggal_retur" type="date" value="{{ $selectedTanggal }}"
                                    class="border border-gray-300 rounded px-2 py-1 max-w-[300px] w-full {{ $formReadonly ? 'bg-gray-200 text-gray-500' : '' }}"
                                    required {{ $formReadonly ? 'readonly' : '' }} />

                                <!-- Retur Dari: Tipe + Referensi -->
                                <label for="return_type" class="text-gray-800 font-medium flex items-center">
                                    Retur Dari <span class="text-red-600 ml-1">*</span>
                                    <span class="ml-1"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Pilih tipe retur (Faktur/Penerimaan/Tanpa Faktur/Uang Muka), lalu cari & pilih referensi jika perlu"
                                        style="cursor: help;">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                </label>
                                <div class="flex flex-nowrap items-center gap-2 w-full">
                                    <select id="return_type" name="return_type"
                                        class="border border-gray-300 rounded px-2 py-1 text-sm shrink-0 w-[120px]">
                                        <option value="invoice">Faktur</option>
                                        <option value="receive">Penerimaan</option>
                                        <option value="no_invoice">Tanpa Faktur</option>
                                        <option value="invoice_dp">Uang Muka</option>
                                    </select>
                                    <div id="return-referensi-wrapper" class="relative flex-1 min-w-0 max-w-[230px]">
                                        <div class="flex items-center border border-gray-300 rounded overflow-hidden">
                                            <input id="return_referensi_search" name="return_referensi_search" type="search"
                                                class="flex-grow outline-none px-2 py-1 text-sm"
                                                placeholder="Cari/Pilih Faktur..." />
                                            <button type="button" id="return-referensi-search-btn" class="px-2 text-gray-600 hover:text-gray-900">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" id="return_referensi_id" value="" />
                                        <div id="dropdown-return-referensi"
                                            class="absolute left-0 right-0 z-10 bg-white border border-gray-300 rounded shadow mt-1 hidden max-h-40 overflow-y-auto text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="grid grid-cols-[150px_1fr] gap-y-4 gap-x-4 text-sm items-start">
                                <!-- Nomor Retur -->
                                <label for="no_retur" class="text-gray-800 font-medium flex items-center">
                                    Nomor Retur<span class="text-red-600 ml-1">*</span>
                                </label>
                                <input id="no_retur" name="no_retur" type="text" value="{{ $no_retur }}"
                                    class="border border-gray-300 rounded px-2 py-1 w-full max-w-[300px] bg-[#f3f4f6] {{ $formReadonly ? 'bg-gray-200 text-gray-500' : '' }}"
                                    readonly />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        @if (!$formReadonly)
                        <button type="button" id="btn-lanjut"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded text-xs">
                            Lanjut
                        </button>
                        @endif
                    </div>
                </form>

                <!-- Table container -->
                <div class="p-2 flex flex-col gap-2">
                    <div class="border border-gray-300 rounded overflow-hidden text-sm">
                        <div class="flex justify-between items-center border border-gray-300 rounded-t bg-[#f9f9f9] px-2 py-2 text-sm">
                            <div class="flex items-center border rounded px-2 py-1 w-[280px]">
                                <input
                                    id="search-barang"
                                    class="flex-grow outline-none placeholder-gray-400"
                                    placeholder="Cari/Pilih Barang & Jasa..."
                                    type="text" />
                                <i class="fas fa-search text-gray-500 ml-2"></i>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-gray-800 font-semibold text-base whitespace-nowrap">
                                    Rincian Barang <span class="text-red-600">*</span>
                                </div>
                            </div>
                        </div>
                        <table class="w-full border-collapse border border-gray-400 text-xs text-center">
                            <thead class="bg-[#607d8b] text-white">
                                <tr>
                                    <th class="border border-gray-400 w-6"><i class="fas fa-sort"></i></th>
                                    <th class="border border-gray-400 px-2 py-1 text-left">Nama Barang</th>
                                    <th class="border border-gray-400 px-2 py-1">Kode #</th>
                                    <th class="border border-gray-400 px-2 py-1">Kuantitas</th>
                                    <th class="border border-gray-400 px-2 py-1">Satuan</th>
                                    <th class="border border-gray-400 px-2 py-1">@Harga</th>
                                    <th class="border border-gray-400 px-2 py-1">Diskon</th>
                                    <th class="border border-gray-400 px-2 py-1">Total Harga</th>
                                </tr>
                            </thead>
                            <tbody id="table-barang-body" class="bg-white">
                                <tr>
                                    <td class="border border-gray-400 px-2 py-3 text-left align-top">≡</td>
                                    <td class="border border-gray-400 px-2 py-3 text-center align-top" colspan="7">
                                        Klik "Lanjut" setelah memilih Vendor dan Referensi (Faktur/Penerimaan) untuk memuat barang.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-end mt-2">
                        <button type="button" id="btn-save-retur-pembelian"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Save Retur Pembelian
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const vendors = @json($vendors ?? []);
    let receiveItems = @json($receiveItems ?? []);
    let purchaseInvoices = @json($purchaseInvoices ?? []);

    const returnTypeSelect = document.getElementById('return_type');
    const returnReferensiSearch = document.getElementById('return_referensi_search');
    const returnReferensiId = document.getElementById('return_referensi_id');
    const returnReferensiWrapper = document.getElementById('return-referensi-wrapper');
    const dropdownReturnReferensi = document.getElementById('dropdown-return-referensi');
    const vendorSearchInput = document.getElementById('vendor_search');
    const vendorNoHidden = document.getElementById('vendor_no_hidden');
    const vendorFetchStatus = document.getElementById('vendor-fetch-status');
    const dropdownVendor = document.getElementById('dropdown-vendor');
    const vendorSearchBtn = document.getElementById('vendor-search-btn');
    const returnReferensiSearchBtn = document.getElementById('return-referensi-search-btn');

    function fetchReferensiByVendor(vendorNo) {
        if (!vendorNo || !vendorNo.trim()) return;
        const urlRiEl = document.getElementById('url_receive_items');
        const urlPiEl = document.getElementById('url_purchase_invoices');
        const urlRi = (urlRiEl && urlRiEl.value) ? urlRiEl.value : '/retur-pembelian/receive-items';
        const urlPi = (urlPiEl && urlPiEl.value) ? urlPiEl.value : '/retur-pembelian/invoices';
        const params = new URLSearchParams({ 'filter.vendorNo': vendorNo.trim() });

        if (vendorFetchStatus) {
            vendorFetchStatus.textContent = 'Memuat Penerimaan Barang & Faktur Pembelian...';
            vendorFetchStatus.classList.remove('hidden');
        }

        Promise.all([
            fetch(urlRi + '?' + params.toString(), { headers: { 'Accept': 'application/json' } }).then(r => r.json()),
            fetch(urlPi + '?' + params.toString(), { headers: { 'Accept': 'application/json' } }).then(r => r.json())
        ]).then(([riRes, piRes]) => {
            receiveItems = riRes.receiveItems || [];
            purchaseInvoices = piRes.purchaseInvoices || [];
            if (vendorFetchStatus) {
                vendorFetchStatus.textContent = 'Berhasil memuat: ' + receiveItems.length + ' Penerimaan, ' + purchaseInvoices.length + ' Faktur.';
                vendorFetchStatus.classList.remove('hidden');
            }
            if (returnReferensiSearch) returnReferensiSearch.value = '';
            if (returnReferensiId) returnReferensiId.value = '';
        }).catch(err => {
            console.error('Error fetch referensi:', err);
            if (vendorFetchStatus) {
                vendorFetchStatus.textContent = 'Gagal memuat data. Silakan coba lagi.';
                vendorFetchStatus.classList.remove('hidden');
            }
        });
    }

    function updateTitle(pageTitle) { document.title = pageTitle; }
    updateTitle('Create Retur Pembelian');

    function getReferensiSource() {
        const tipe = returnTypeSelect ? returnTypeSelect.value : '';
        if (tipe === 'invoice' || tipe === 'invoice_dp') return { data: purchaseInvoices, label: 'Faktur Pembelian' };
        if (tipe === 'receive') return { data: receiveItems, label: 'Penerimaan Barang' };
        return { data: [], label: '' };
    }
    function getReferensiPlaceholder() {
        const src = getReferensiSource();
        return src.label ? 'Cari/Pilih ' + src.label + '...' : '';
    }
    function toggleReferensiVisibility() {
        const tipe = returnTypeSelect ? returnTypeSelect.value : '';
        const isNoInvoice = tipe === 'no_invoice';
        if (returnReferensiWrapper) {
            returnReferensiWrapper.style.display = isNoInvoice ? 'none' : '';
        }
        if (isNoInvoice) {
            if (returnReferensiSearch) { returnReferensiSearch.value = ''; returnReferensiSearch.placeholder = ''; }
            if (returnReferensiId) returnReferensiId.value = '';
            if (dropdownReturnReferensi) { dropdownReturnReferensi.innerHTML = ''; dropdownReturnReferensi.classList.add('hidden'); }
        } else {
            if (returnReferensiSearch) returnReferensiSearch.placeholder = getReferensiPlaceholder();
        }
    }

    // ---- Dropdown Vendor ----
    function showDropdownVendor(input) {
        if (!dropdownVendor) return;
        const query = (input && input.value) ? input.value.toLowerCase().trim() : '';
        if (query === '') { showAllVendor(); return; }
        const result = vendors.filter(v =>
            (v.name && v.name.toLowerCase().includes(query)) ||
            (v.vendorNo && v.vendorNo.toLowerCase().includes(query))
        );
        dropdownVendor.innerHTML = '';
        if (result.length === 0) {
            const noResult = document.createElement('div');
            noResult.className = 'px-3 py-2 text-center text-gray-500 border-b';
            noResult.textContent = 'Tidak ada Vendor yang cocok dengan "' + query + '"';
            dropdownVendor.appendChild(noResult);
        } else {
            const header = document.createElement('div');
            header.className = 'px-3 py-2 bg-blue-50 border-b font-semibold text-sm text-blue-700';
            header.innerHTML = '<i class="fas fa-search mr-2"></i>Hasil: ' + result.length + ' Vendor';
            dropdownVendor.appendChild(header);
            result.forEach(v => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer border-b transition-colors duration-150';
                item.innerHTML = '<div class="font-semibold text-sm text-gray-800">' + (v.name || '') + '</div><div class="text-sm text-gray-500">' + (v.vendorNo || '') + '</div>';
                item.onclick = function() { selectVendor(v); };
                dropdownVendor.appendChild(item);
            });
        }
        dropdownVendor.classList.remove('hidden');
    }
    function showAllVendor() {
        if (!dropdownVendor) return;
        dropdownVendor.innerHTML = '';
        if (vendors.length === 0) {
            const noData = document.createElement('div');
            noData.className = 'px-3 py-2 text-center text-gray-500 border-b';
            noData.textContent = 'Tidak ada data Vendor';
            dropdownVendor.appendChild(noData);
        } else {
            const header = document.createElement('div');
            header.className = 'px-3 py-2 bg-gray-50 border-b font-semibold text-sm text-gray-700';
            header.innerHTML = '<i class="fas fa-list mr-2"></i>Semua Vendor (' + vendors.length + ')';
            dropdownVendor.appendChild(header);
            const maxShow = 50;
            vendors.slice(0, maxShow).forEach(v => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer border-b';
                item.innerHTML = '<div class="font-semibold text-sm text-gray-800">' + (v.name || '') + '</div><div class="text-sm text-gray-500">' + (v.vendorNo || '') + '</div>';
                item.onclick = function() { selectVendor(v); };
                dropdownVendor.appendChild(item);
            });
            if (vendors.length > maxShow) {
                const more = document.createElement('div');
                more.className = 'px-3 py-2 bg-blue-50 border-b text-sm text-blue-600 text-center';
                more.textContent = 'Menampilkan ' + maxShow + ' dari ' + vendors.length + '. Ketik untuk mencari.';
                dropdownVendor.appendChild(more);
            }
        }
        dropdownVendor.classList.remove('hidden');
    }
    function selectVendor(v) {
        if (vendorSearchInput) vendorSearchInput.value = v.name || v.vendorNo || '';
        if (vendorNoHidden) vendorNoHidden.value = v.vendorNo || '';
        if (dropdownVendor) dropdownVendor.classList.add('hidden');
        if (v.vendorNo) fetchReferensiByVendor(v.vendorNo);
    }

    // ---- Dropdown Referensi (Faktur Pembelian / Penerimaan Barang) ----
    function getReferensiDisplayName(row) {
        const num = row.number || row.no || '';
        const vend = row.vendor;
        const vendName = (vend && typeof vend === 'object' && vend.name) ? vend.name : (typeof vend === 'string' ? vend : '');
        return vendName ? num + ' - ' + vendName : num;
    }
    function showDropdownReferensi(input) {
        if (!dropdownReturnReferensi || !returnReferensiWrapper || returnReferensiWrapper.style.display === 'none') return;
        const src = getReferensiSource();
        const data = src.data || [];
        const query = (input && input.value) ? input.value.toLowerCase().trim() : '';
        const filtered = query === ''
            ? data
            : data.filter(r => {
                const name = getReferensiDisplayName(r).toLowerCase();
                const num = (r.number || r.no || '').toLowerCase();
                return name.includes(query) || num.includes(query);
            });
        dropdownReturnReferensi.innerHTML = '';
        if (filtered.length === 0) {
            const noResult = document.createElement('div');
            noResult.className = 'px-3 py-2 text-center text-gray-500 border-b';
            noResult.textContent = query ? 'Tidak ada yang cocok dengan "' + query + '"' : 'Tidak ada data ' + src.label;
            dropdownReturnReferensi.appendChild(noResult);
        } else {
            const header = document.createElement('div');
            header.className = 'px-3 py-2 bg-blue-50 border-b font-semibold text-sm text-blue-700';
            header.innerHTML = '<i class="fas fa-search mr-2"></i>Hasil: ' + filtered.length + ' ' + src.label;
            dropdownReturnReferensi.appendChild(header);
            filtered.forEach(row => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer border-b';
                item.textContent = getReferensiDisplayName(row);
                const id = row.number || row.no || '';
                item.onclick = function() {
                    if (returnReferensiSearch) returnReferensiSearch.value = getReferensiDisplayName(row);
                    if (returnReferensiId) returnReferensiId.value = id;
                    dropdownReturnReferensi.classList.add('hidden');
                };
                dropdownReturnReferensi.appendChild(item);
            });
        }
        dropdownReturnReferensi.classList.remove('hidden');
    }
    function showAllReferensi() {
        if (!dropdownReturnReferensi || !returnReferensiWrapper || returnReferensiWrapper.style.display === 'none') return;
        const src = getReferensiSource();
        const data = src.data || [];
        dropdownReturnReferensi.innerHTML = '';
        if (data.length === 0) {
            const noData = document.createElement('div');
            noData.className = 'px-3 py-2 text-center text-gray-500 border-b';
            noData.textContent = 'Tidak ada data ' + src.label;
            dropdownReturnReferensi.appendChild(noData);
        } else {
            const header = document.createElement('div');
            header.className = 'px-3 py-2 bg-gray-50 border-b font-semibold text-sm text-gray-700';
            header.innerHTML = '<i class="fas fa-list mr-2"></i>Semua ' + src.label + ' (' + data.length + ')';
            dropdownReturnReferensi.appendChild(header);
            const maxShow = 50;
            data.slice(0, maxShow).forEach(row => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer border-b';
                item.textContent = getReferensiDisplayName(row);
                const id = row.number || row.no || '';
                item.onclick = function() {
                    if (returnReferensiSearch) returnReferensiSearch.value = getReferensiDisplayName(row);
                    if (returnReferensiId) returnReferensiId.value = id;
                    dropdownReturnReferensi.classList.add('hidden');
                };
                dropdownReturnReferensi.appendChild(item);
            });
            if (data.length > maxShow) {
                const more = document.createElement('div');
                more.className = 'px-3 py-2 bg-blue-50 border-b text-sm text-blue-600 text-center';
                more.textContent = 'Menampilkan ' + maxShow + ' dari ' + data.length + '. Ketik untuk mencari.';
                dropdownReturnReferensi.appendChild(more);
            }
        }
        dropdownReturnReferensi.classList.remove('hidden');
    }

    // ---- Event: Tipe Retur berubah ----
    if (returnTypeSelect) {
        returnTypeSelect.addEventListener('change', function() {
            toggleReferensiVisibility();
            if (returnReferensiSearch) returnReferensiSearch.value = '';
            if (returnReferensiId) returnReferensiId.value = '';
        });
    }

    // ---- Event: Vendor ----
    if (vendorSearchBtn) {
        vendorSearchBtn.addEventListener('click', function() {
            if (vendorSearchInput && vendorSearchInput.value.trim() === '') showAllVendor();
            else showDropdownVendor(vendorSearchInput);
        });
    }
    if (vendorSearchInput) {
        vendorSearchInput.addEventListener('input', function() { showDropdownVendor(vendorSearchInput); });
        vendorSearchInput.addEventListener('focus', function() {
            if (vendorSearchInput.value.trim() === '') showAllVendor();
            else showDropdownVendor(vendorSearchInput);
        });
        vendorSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && dropdownVendor) dropdownVendor.classList.add('hidden');
        });
    }

    // ---- Event: Referensi (Faktur Pembelian / Penerimaan Barang) ----
    if (returnReferensiSearchBtn) {
        returnReferensiSearchBtn.addEventListener('click', function() {
            if (!returnReferensiWrapper || returnReferensiWrapper.style.display === 'none') return;
            if (returnReferensiSearch.value.trim() === '') showAllReferensi();
            else showDropdownReferensi(returnReferensiSearch);
        });
    }
    if (returnReferensiSearch) {
        returnReferensiSearch.addEventListener('input', function() { showDropdownReferensi(returnReferensiSearch); });
        returnReferensiSearch.addEventListener('focus', function() {
            if (returnReferensiWrapper && returnReferensiWrapper.style.display === 'none') return;
            if (returnReferensiSearch.value.trim() === '') showAllReferensi();
            else showDropdownReferensi(returnReferensiSearch);
        });
        returnReferensiSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && dropdownReturnReferensi) dropdownReturnReferensi.classList.add('hidden');
        });
    }

    document.addEventListener('click', function(e) {
        const vendorWrap = vendorSearchInput && vendorSearchInput.closest('.relative');
        if (dropdownVendor && vendorWrap && !vendorWrap.contains(e.target) && !dropdownVendor.contains(e.target)) {
            dropdownVendor.classList.add('hidden');
        }
        if (returnReferensiWrapper && dropdownReturnReferensi && !returnReferensiWrapper.contains(e.target) && !dropdownReturnReferensi.contains(e.target)) {
            dropdownReturnReferensi.classList.add('hidden');
        }
    });

    // --- Data detail items ---
    let detailItems = [];

    function formatCurrency(num) {
        const n = parseFloat(num);
        if (isNaN(n)) return '0';
        return n.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    function fillTableWithDetailItems(items) {
        const tableBody = document.getElementById('table-barang-body');
        if (!tableBody) return;
        tableBody.innerHTML = '';

        if (!items || items.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = '<td class="border border-gray-400 px-2 py-3 text-left align-top">≡</td><td class="border border-gray-400 px-2 py-3 text-center align-top" colspan="7">Belum ada data barang</td>';
            tableBody.appendChild(emptyRow);
            return;
        }

        items.forEach(item => {
            const row = document.createElement('tr');
            const unitPrice = formatCurrency(item.unitPrice || 0);
            const discount = formatCurrency(item.itemCashDiscount || 0);
            const totalPrice = formatCurrency(item.totalPrice || 0);

            row.innerHTML = '<td class="border border-gray-400 px-2 py-3 text-left align-top">≡</td>' +
                '<td class="border border-gray-400 px-2 py-3 text-left align-top">' + (item.item && item.item.name ? item.item.name : 'N/A') + '</td>' +
                '<td class="border border-gray-400 px-2 py-3 align-top">' + (item.item && item.item.no ? item.item.no : 'N/A') + '</td>' +
                '<td class="border border-gray-400 px-2 py-3 align-top">' + (item.quantity || 0) + '</td>' +
                '<td class="border border-gray-400 px-2 py-3 align-top">' + (item.itemUnit && item.itemUnit.name ? item.itemUnit.name : 'N/A') + '</td>' +
                '<td class="border border-gray-400 px-2 py-3 align-top">' + unitPrice + '</td>' +
                '<td class="border border-gray-400 px-2 py-3 align-top">' + discount + '</td>' +
                '<td class="border border-gray-400 px-2 py-3 align-top">' + totalPrice + '</td>';

            tableBody.appendChild(row);
        });
    }

    // Tombol Lanjut: muat detail barang dari referensi
    const btnLanjut = document.getElementById('btn-lanjut');
    if (btnLanjut) {
        btnLanjut.addEventListener('click', function() {
            const returnType = document.getElementById('return_type');
            const tipe = returnType ? returnType.value : '';

            if (!vendorNoHidden || !vendorNoHidden.value.trim()) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Pilih Vendor terlebih dahulu.' });
                else alert('Pilih Vendor terlebih dahulu.');
                return;
            }

            if (tipe === 'no_invoice') {
                detailItems = [];
                fillTableWithDetailItems([]);
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'info', title: 'Info', text: 'Tipe retur Tanpa Faktur tidak memerlukan referensi. Silakan tambahkan barang manual jika diperlukan.', timer: 3000, showConfirmButton: false });
                return;
            }

            if (!returnReferensiId || !returnReferensiId.value.trim()) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Pilih referensi Retur Dari (Faktur/Penerimaan) terlebih dahulu.' });
                else alert('Pilih referensi Retur Dari terlebih dahulu.');
                return;
            }

            const urlEl = document.getElementById('url_referensi_detail');
            const url = (urlEl && urlEl.value) ? urlEl.value : '';
            if (!url) return;

            const params = new URLSearchParams({ return_type: tipe, number: returnReferensiId.value.trim() });
            btnLanjut.disabled = true;
            btnLanjut.textContent = 'Loading...';
            fetch(url + '?' + params.toString(), { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(function(data) {
                    if (data.success && data.detailItems && data.detailItems.length > 0) {
                        detailItems = data.detailItems;
                        fillTableWithDetailItems(detailItems);
                        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data barang dimuat: ' + detailItems.length + ' item.', timer: 2000, showConfirmButton: false });
                    } else {
                        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Info', text: data.message || 'Tidak ada detail barang.' });
                        else alert(data.message || 'Tidak ada detail barang.');
                    }
                })
                .catch(function(err) {
                    console.error(err);
                    if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memuat detail referensi.' });
                    else alert('Gagal memuat detail referensi.');
                })
                .finally(function() {
                    btnLanjut.disabled = false;
                    btnLanjut.textContent = 'Lanjut';
                });
        });
    }

    function formatDetailItemsForSubmission(items) {
        if (!items || items.length === 0) return [];
        return items.map(function(item) {
            return {
                kode: (item.item && item.item.no) ? item.item.no : '',
                kuantitas: item.quantity != null ? item.quantity : 0,
                harga: item.unitPrice !== undefined && item.unitPrice !== null ? item.unitPrice : 0,
                diskon: item.itemCashDiscount !== undefined && item.itemCashDiscount !== null ? item.itemCashDiscount : 0
            };
        });
    }

    // Tombol Save Retur Pembelian
    const btnSaveRetur = document.getElementById('btn-save-retur-pembelian');
    if (btnSaveRetur) {
        btnSaveRetur.addEventListener('click', function() {
            const form = document.getElementById('returPembelianForm');
            const returnType = document.getElementById('return_type');
            const tipe = returnType ? returnType.value : '';

            if (!vendorNoHidden || !vendorNoHidden.value.trim()) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Validasi', text: 'Vendor wajib dipilih.' });
                else alert('Vendor wajib dipilih.');
                return;
            }

            if (tipe !== 'no_invoice' && (!returnReferensiId || !returnReferensiId.value.trim())) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Validasi', text: 'Referensi Retur Dari wajib dipilih.' });
                else alert('Referensi Retur Dari wajib dipilih.');
                return;
            }

            if (tipe !== 'no_invoice' && (!detailItems || detailItems.length === 0)) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Validasi', text: 'Klik Lanjut untuk memuat barang terlebih dahulu.' });
                else alert('Klik Lanjut untuk memuat barang terlebih dahulu.');
                return;
            }

            // Set referensi id ke input hidden yang sesuai berdasarkan tipe
            const fakturIdEl = document.getElementById('faktur_pembelian_id');
            const penerimaanIdEl = document.getElementById('penerimaan_barang_id');
            const refVal = returnReferensiId ? returnReferensiId.value.trim() : '';

            if (fakturIdEl) fakturIdEl.value = '';
            if (penerimaanIdEl) penerimaanIdEl.value = '';

            if ((tipe === 'invoice' || tipe === 'invoice_dp') && fakturIdEl) {
                fakturIdEl.value = refVal;
            } else if (tipe === 'receive' && penerimaanIdEl) {
                penerimaanIdEl.value = refVal;
            }

            const formatted = formatDetailItemsForSubmission(detailItems);
            const existingDetailInputs = form.querySelectorAll('input[name^="detailItems"]');
            existingDetailInputs.forEach(function(input) { input.remove(); });

            formatted.forEach(function(item, index) {
                ['kode', 'kuantitas', 'harga', 'diskon'].forEach(function(field) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'detailItems[' + index + '][' + field + ']';
                    input.value = item[field] !== undefined && item[field] !== null ? item[field] : '';
                    form.appendChild(input);
                });
            });

            document.getElementById('form_submitted').value = '1';
            form.submit();
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleReferensiVisibility();
        if (typeof $ !== 'undefined' && $('[data-toggle="tooltip"]').length) $('[data-toggle="tooltip"]').tooltip();

        // Tampilkan error dari Laravel jika ada
        const errorsEl = document.getElementById('laravel-errors');
        if (errorsEl) {
            const serverError = errorsEl.getAttribute('data-error');
            const validationErrors = errorsEl.getAttribute('data-validation-errors');
            if (serverError && typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: serverError });
            }
            if (validationErrors) {
                try {
                    const errors = JSON.parse(validationErrors);
                    if (errors.length > 0 && typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: errors.map(e => '• ' + e).join('<br>') });
                    }
                } catch(e) {}
            }
        }
    });
})();
</script>
@endsection
