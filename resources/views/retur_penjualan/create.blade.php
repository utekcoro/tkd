@extends('layout.main')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Form Retur Penjualan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('retur_penjualan.index') }}">Retur Penjualan</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <form id="returPenjualanForm" method="POST" action="{{ route('retur_penjualan.store') }}" class="p-3 space-y-3">
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
                    <input type="hidden" id="url_delivery_orders" value="{{ route('retur_penjualan.delivery_orders') }}">
                    <input type="hidden" id="url_sales_invoices" value="{{ route('retur_penjualan.sales_invoices') }}">
                    <input type="hidden" id="url_referensi_detail" value="{{ route('retur_penjualan.referensi_detail') }}">
                    <input type="hidden" id="pelanggan_id_hidden" name="pelanggan_id" value="">
                    <input type="hidden" id="pengiriman_pesanan_id" name="pengiriman_pesanan_id" value="">
                    <input type="hidden" id="faktur_penjualan_id" name="faktur_penjualan_id" value="">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="grid grid-cols-[150px_1fr] gap-y-4 gap-x-4 text-sm items-start">
                                <!-- Pelanggan -->
                                <label for="pelanggan_id" class="text-gray-800 font-medium flex items-center">
                                    Pelanggan <span class="text-red-600 ml-1">*</span>
                                    <span class="ml-1"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Silakan cari & pilih pelanggan dari dropdown"
                                        style="cursor: help;">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                </label>

                                <div class="relative max-w-md w-full">
                                    <input type="hidden" id="pelanggan_customer_no" name="pelanggan_customer_no" value="" />
                                    <div class="flex items-center border border-gray-300 rounded overflow-hidden max-w-[300px]">
                                        <input
                                            id="pelanggan_id"
                                            type="search"
                                            placeholder="Cari/Pilih Pelanggan..."
                                            class="flex-grow px-2 py-1 outline-none text-sm"
                                            required />
                                        <button type="button" id="pelanggan-search-btn" class="px-2 text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <!-- Dropdown akan muncul di sini -->
                                    <div id="dropdown-pelanggan" class="absolute left-0 right-0 z-10 bg-white border border-gray-300 rounded shadow mt-1 hidden max-h-40 overflow-y-auto text-sm max-w-[300px]">

                                    </div>
                                    <small id="pelanggan-fetch-status" class="text-gray-500 hidden"></small>
                                </div>

                                <!-- Tanggal -->
                                <label for="tanggal_retur" class="text-gray-800 font-medium flex items-center">
                                    Tanggal<span class="text-red-600 ml-1">*</span>
                                </label>
                                <input id="tanggal_retur" name="tanggal_retur" type="date" value="{{ $selectedTanggal }}"
                                    class="border border-gray-300 rounded px-2 py-1 max-w-[300px] w-full {{ $formReadonly ? 'bg-gray-200 text-gray-500' : '' }}"
                                    required {{ $formReadonly ? 'readonly' : '' }} />

                                <!-- Pengembalian -->
                                <label for="return_status_type" class="text-gray-800 font-medium flex items-center">
                                    Pengembalian <span class="text-red-600 ml-1">*</span>
                                    <span class="ml-1" data-toggle="tooltip" data-placement="top"
                                        title="Not Returned: belum dikembalikan; Partially Returned: sebagian dikembalikan (atur per item); Returned: sudah dikembalikan semua"
                                        style="cursor: help;"><i class="fas fa-info-circle"></i></span>
                                </label>
                                <div class="max-w-[300px]">
                                    <select id="return_status_type" name="return_status_type"
                                        class="border border-gray-300 rounded px-2 py-1 text-sm w-full">
                                        <option value="not_returned">Not Returned (Tidak Dikembalikan)</option>
                                        <option value="partially_returned">Partially Returned (Sebagian Dikembalikan)</option>
                                        <option value="returned">Returned (Dikembalikan)</option>
                                    </select>
                                </div>

                                <!-- Retur Dari: Tipe + Referensi (Faktur/Pengiriman) -->
                                <label for="retur_dari_tipe" class="text-gray-800 font-medium flex items-center">
                                    Retur Dari <span class="text-red-600 ml-1">*</span>
                                    <span class="ml-1"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Pilih tipe retur (Faktur/Pengiriman/Tanpa Faktur/Uang Muka), lalu cari & pilih referensi jika perlu"
                                        style="cursor: help;">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                </label>
                                <div class="flex flex-nowrap items-center gap-2 w-full">
                                    <!-- Select Tipe Return -->
                                    <select id="return_type" name="return_type"
                                        class="border border-gray-300 rounded px-2 py-1 text-sm shrink-0 w-[120px]">
                                        <option value="invoice">Faktur</option>
                                        <option value="delivery">Pengiriman</option>
                                        <option value="no_invoice">Tanpa Faktur</option>
                                        <option value="invoice_dp">Uang Muka</option>
                                    </select>
                                    <!-- Input Cari/Pilih Faktur atau Pengiriman (seperti pelanggan) -->
                                    <div id="return-referensi-wrapper" class="relative flex-1 min-w-0 max-w-[230px]">
                                        <div class="flex items-center border border-gray-300 rounded overflow-hidden">
                                            <input id="return_referensi_search" name="return_referensi_search" type="search"
                                                class="flex-grow outline-none px-2 py-1 text-sm"
                                                placeholder="Cari/Pilih Faktur..." />
                                            <button type="button" id="return-referensi-search-btn" class="px-2 text-gray-600 hover:text-gray-900">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                        <input type="hidden" id="return_referensi_id" name="return_referensi_id" value="" />
                                        <!-- Dropdown akan muncul di sini -->
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
                            <!-- Search kiri -->
                            <div class="flex items-center border rounded px-2 py-1 w-[280px]">
                                <input
                                    id="search-barang"
                                    class="flex-grow outline-none placeholder-gray-400"
                                    placeholder="Cari/Pilih Barang & Jasa..."
                                    type="text" />
                                <i class="fas fa-search text-gray-500 ml-2"></i>
                            </div>
                            <div class="flex items-center gap-4">
                                <!-- Label kanan -->
                                <div class="text-gray-800 font-semibold text-base whitespace-nowrap">
                                    Rincian Barang <span class="text-red-600">*</span>
                                </div>
                            </div>
                        </div>
                        <table class="w-full border-collapse border border-gray-400 text-xs text-center">
                            <thead class="bg-[#607d8b] text-white">
                                <tr>
                                    <th class="border border-gray-400 w-6">
                                        <i class="fas fa-sort">
                                        </i>
                                    </th>
                                    <th class="border border-gray-400 px-2 py-1 text-left">
                                        Nama Barang
                                    </th>
                                    <th class="border border-gray-400 px-2 py-1">
                                        Kode #
                                    </th>
                                    <th class="border border-gray-400 px-2 py-1">
                                        Kuantitas
                                    </th>
                                    <th class="border border-gray-400 px-2 py-1">
                                        Satuan
                                    </th>
                                    <th class="border border-gray-400 px-2 py-1">
                                        @Harga
                                    </th>
                                    <th class="border border-gray-400 px-2 py-1">
                                        Diskon
                                    </th>
                                    <th class="border border-gray-400 px-2 py-1">
                                        Total Harga
                                    </th>
                                    <th id="th-status-retur" class="border border-gray-400 px-2 py-1" style="display: none;">
                                        Status Retur
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="table-barang-body" class="bg-white">
                                @if (isset($detailItems) && count($detailItems) > 0)
                                @foreach ($detailItems as $item)
                                <tr>
                                    <td class="border border-gray-400 px-2 py-3 text-left align-top">
                                        ≡
                                    </td>
                                    <td class="border border-gray-400 px-2 py-3 text-left align-top">
                                        {{ $item['item']['name'] }}
                                    </td>
                                    <td class="border border-gray-400 px-2 py-3 align-top">
                                        {{ $item['item']['no'] }}
                                    </td>
                                    <td class="border border-gray-400 px-2 py-3 align-top">
                                        {{ $item['quantity'] }}
                                    </td>
                                    <td class="border border-gray-400 px-2 py-3 align-top">
                                        {{ $item['itemUnit']['name'] }}
                                    </td>
                                    <td class="border border-gray-400 px-2 py-3 align-top">
                                        {{ $item['unitPrice'] }}
                                    </td>
                                    <td class="border border-gray-400 px-2 py-3 align-top">
                                        {{ $item['itemCashDiscount'] }}
                                    </td>
                                    <td class="border border-gray-400 px-2 py-3 align-top">
                                        {{ $item['totalPrice'] }}
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td class="border border-gray-400 px-2 py-3 text-left align-top">≡</td>
                                    <td class="border border-gray-400 px-2 py-3 text-center align-top" colspan="8">
                                        Klik "Lanjut" setelah memilih Pelanggan dan Referensi (Faktur/Pengiriman) untuk memuat barang.
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-end mt-2">
                        <button type="button" id="btn-save-retur-penjualan"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Save Retur Penjualan
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Status Retur Per Item (untuk Partially Returned) -->
<div class="modal fade" id="modalReturnDetailStatus" tabindex="-1" role="dialog" aria-labelledby="modalReturnDetailStatusLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalReturnDetailStatusLabel">Status Pengembalian Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="modalItemName" class="font-weight-bold text-gray-800 mb-2"></p>
                <label for="modalReturnDetailStatusSelect">Status</label>
                <select id="modalReturnDetailStatusSelect" class="form-control border border-gray-300 rounded px-2 py-1 w-full">
                    <option value="NOT_RETURNED">NOT_RETURNED (Belum Dikembalikan)</option>
                    <option value="RETURNED">RETURNED (Dikembalikan)</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" id="modalReturnDetailStatusSave" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // Data dari backend (controller) - salesInvoices & deliveryOrders di-fetch via AJAX saat customer dipilih
    const pelanggan = @json($pelanggan ?? []);
    let salesInvoices = @json($salesInvoices ?? []);
    let deliveryOrders = @json($deliveryOrders ?? []);

    const returnTypeSelect = document.getElementById('return_type');
    const returnReferensiSearch = document.getElementById('return_referensi_search');
    const returnReferensiId = document.getElementById('return_referensi_id');
    const returnReferensiWrapper = document.getElementById('return-referensi-wrapper');
    const dropdownReturnReferensi = document.getElementById('dropdown-return-referensi');
    const pelangganInput = document.getElementById('pelanggan_id');
    const pelangganCustomerNoInput = document.getElementById('pelanggan_customer_no');
    const pelangganFetchStatus = document.getElementById('pelanggan-fetch-status');
    const dropdownPelanggan = document.getElementById('dropdown-pelanggan');
    const pelangganSearchBtn = document.getElementById('pelanggan-search-btn');
    const returnReferensiSearchBtn = document.getElementById('return-referensi-search-btn');

    // Fetch delivery orders & sales invoices via AJAX dengan filter.customerNo
    function fetchReferensiByCustomer(customerNo) {
        if (!customerNo || !customerNo.trim()) return;
        const urlDoEl = document.getElementById('url_delivery_orders');
        const urlSiEl = document.getElementById('url_sales_invoices');
        const urlDo = (urlDoEl && urlDoEl.value) ? urlDoEl.value : '/retur-penjualan/delivery-orders';
        const urlSi = (urlSiEl && urlSiEl.value) ? urlSiEl.value : '/retur-penjualan/sales-invoices';
        const params = new URLSearchParams({ 'filter.customerNo': customerNo.trim() });

        if (pelangganFetchStatus) {
            pelangganFetchStatus.textContent = 'Memuat Delivery Order & Faktur Penjualan...';
            pelangganFetchStatus.classList.remove('hidden');
        }

        Promise.all([
            fetch(urlDo + '?' + params.toString(), { headers: { 'Accept': 'application/json' } }).then(r => r.json()),
            fetch(urlSi + '?' + params.toString(), { headers: { 'Accept': 'application/json' } }).then(r => r.json())
        ]).then(([doRes, siRes]) => {
            deliveryOrders = doRes.deliveryOrders || [];
            salesInvoices = siRes.salesInvoices || [];
            if (pelangganFetchStatus) {
                pelangganFetchStatus.textContent = 'Berhasil memuat: ' + deliveryOrders.length + ' Pengiriman, ' + salesInvoices.length + ' Faktur.';
                pelangganFetchStatus.classList.remove('hidden');
            }
            // Kosongkan referensi yang sudah dipilih karena data berubah
            if (returnReferensiSearch) { returnReferensiSearch.value = ''; }
            if (returnReferensiId) returnReferensiId.value = '';
        }).catch(err => {
            console.error('Error fetch referensi:', err);
            if (pelangganFetchStatus) {
                pelangganFetchStatus.textContent = 'Gagal memuat data. Silakan coba lagi.';
                pelangganFetchStatus.classList.remove('hidden');
            }
        });
    }

    function updateTitle(pageTitle) {
        document.title = pageTitle;
    }

    updateTitle('Create Retur Penjualan');

    // Helper: sumber data referensi & placeholder berdasarkan tipe
    function getReferensiSource() {
        const tipe = returnTypeSelect ? returnTypeSelect.value : '';
        if (tipe === 'invoice' || tipe === 'invoice_dp') return { data: salesInvoices, label: 'Faktur' };
        if (tipe === 'delivery') return { data: deliveryOrders, label: 'Pengiriman' };
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

    // ---- Dropdown Pelanggan (contoh pola sales_cashier) ----
    function showDropdownPelanggan(input) {
        if (!dropdownPelanggan) return;
        const query = (input && input.value) ? input.value.toLowerCase().trim() : '';
        if (query === '') {
            showAllPelanggan();
            return;
        }
        const result = pelanggan.filter(p =>
            (p.name && p.name.toLowerCase().includes(query)) ||
            (p.customerNo && p.customerNo.toLowerCase().includes(query))
        );
        dropdownPelanggan.innerHTML = '';
        if (result.length === 0) {
            const noResult = document.createElement('div');
            noResult.className = 'px-3 py-2 text-center text-gray-500 border-b';
            noResult.textContent = 'Tidak ada Pelanggan yang cocok dengan "' + query + '"';
            dropdownPelanggan.appendChild(noResult);
        } else {
            const header = document.createElement('div');
            header.className = 'px-3 py-2 bg-blue-50 border-b font-semibold text-sm text-blue-700';
            header.innerHTML = '<i class="fas fa-search mr-2"></i>Hasil: ' + result.length + ' Pelanggan';
            dropdownPelanggan.appendChild(header);
            result.forEach(p => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer border-b transition-colors duration-150';
                item.innerHTML = '<div class="font-semibold text-sm text-gray-800">' + (p.name || '') + '</div><div class="text-sm text-gray-500">' + (p.customerNo || '') + '</div>';
                item.onclick = function() {
                    if (pelangganInput) pelangganInput.value = p.name || p.customerNo || '';
                    if (pelangganCustomerNoInput) pelangganCustomerNoInput.value = p.customerNo || '';
                    const pelangganIdHidden = document.getElementById('pelanggan_id_hidden');
                    if (pelangganIdHidden) pelangganIdHidden.value = p.customerNo || '';
                    dropdownPelanggan.classList.add('hidden');
                    if (p.customerNo) fetchReferensiByCustomer(p.customerNo);
                };
                dropdownPelanggan.appendChild(item);
            });
        }
        dropdownPelanggan.classList.remove('hidden');
    }
    function showAllPelanggan() {
        if (!dropdownPelanggan) return;
        dropdownPelanggan.innerHTML = '';
        if (pelanggan.length === 0) {
            const noData = document.createElement('div');
            noData.className = 'px-3 py-2 text-center text-gray-500 border-b';
            noData.textContent = 'Tidak ada data Pelanggan';
            dropdownPelanggan.appendChild(noData);
        } else {
            const header = document.createElement('div');
            header.className = 'px-3 py-2 bg-gray-50 border-b font-semibold text-sm text-gray-700';
            header.innerHTML = '<i class="fas fa-list mr-2"></i>Semua Pelanggan (' + pelanggan.length + ')';
            dropdownPelanggan.appendChild(header);
            const maxShow = 50;
            pelanggan.slice(0, maxShow).forEach(p => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer border-b';
                item.innerHTML = '<div class="font-semibold text-sm text-gray-800">' + (p.name || '') + '</div><div class="text-sm text-gray-500">' + (p.customerNo || '') + '</div>';
                item.onclick = function() {
                    if (pelangganInput) pelangganInput.value = p.name || p.customerNo || '';
                    if (pelangganCustomerNoInput) pelangganCustomerNoInput.value = p.customerNo || '';
                    const pelangganIdHidden = document.getElementById('pelanggan_id_hidden');
                    if (pelangganIdHidden) pelangganIdHidden.value = p.customerNo || '';
                    dropdownPelanggan.classList.add('hidden');
                    if (p.customerNo) fetchReferensiByCustomer(p.customerNo);
                };
                dropdownPelanggan.appendChild(item);
            });
            if (pelanggan.length > maxShow) {
                const more = document.createElement('div');
                more.className = 'px-3 py-2 bg-blue-50 border-b text-sm text-blue-600 text-center';
                more.textContent = 'Menampilkan ' + maxShow + ' dari ' + pelanggan.length + '. Ketik untuk mencari.';
                dropdownPelanggan.appendChild(more);
            }
        }
        dropdownPelanggan.classList.remove('hidden');
    }

    // ---- Dropdown Referensi (Faktur / Pengiriman) ----
    function getReferensiDisplayName(row) {
        const num = row.number || row.no || '';
        const cust = row.customer;
        const custName = (cust && typeof cust === 'object' && cust.name) ? cust.name : (typeof cust === 'string' ? cust : '');
        return custName ? num + ' - ' + custName : num;
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
                const id = row.id != null ? row.id : (row.number || row.no);
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
                const id = row.id != null ? row.id : (row.number || row.no);
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

    // ---- Event: Pelanggan ----
    if (pelangganSearchBtn) {
        pelangganSearchBtn.addEventListener('click', function() {
            if (pelangganInput && pelangganInput.value.trim() === '') showAllPelanggan();
            else showDropdownPelanggan(pelangganInput);
        });
    }
    if (pelangganInput) {
        pelangganInput.addEventListener('input', function() { showDropdownPelanggan(pelangganInput); });
        pelangganInput.addEventListener('focus', function() {
            if (pelangganInput.value.trim() === '') showAllPelanggan();
            else showDropdownPelanggan(pelangganInput);
        });
        pelangganInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && dropdownPelanggan) dropdownPelanggan.classList.add('hidden');
        });
    }

    // ---- Event: Referensi (Faktur/Pengiriman) ----
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

    // Klik di luar untuk menutup dropdown
    document.addEventListener('click', function(e) {
        const pelangganWrap = pelangganInput && pelangganInput.closest('.relative');
        if (dropdownPelanggan && pelangganWrap && !pelangganWrap.contains(e.target) && !dropdownPelanggan.contains(e.target)) {
            dropdownPelanggan.classList.add('hidden');
        }
        if (returnReferensiWrapper && dropdownReturnReferensi && returnReferensiWrapper.contains(e.target) === false && dropdownReturnReferensi.contains(e.target) === false) {
            dropdownReturnReferensi.classList.add('hidden');
        }
    });

    // --- Data detail items (diisi setelah Lanjut) ---
    let detailItems = [];

    function getReturnStatusType() {
        const el = document.getElementById('return_status_type');
        return el ? el.value : 'not_returned';
    }

    function formatCurrency(num) {
        const n = parseFloat(num);
        if (isNaN(n)) return '0';
        return n.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    function fillTableWithDetailItems(items) {
        const tableBody = document.getElementById('table-barang-body');
        const thStatusRetur = document.getElementById('th-status-retur');
        const isPartially = getReturnStatusType() === 'partially_returned';

        if (!tableBody) return;
        tableBody.innerHTML = '';

        if (thStatusRetur) thStatusRetur.style.display = isPartially ? '' : 'none';

        if (!items || items.length === 0) {
            const colspan = 8;
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = '<td class="border border-gray-400 px-2 py-3 text-left align-top">≡</td><td class="border border-gray-400 px-2 py-3 text-center align-top" colspan="' + colspan + '">Belum ada data barang</td>';
            tableBody.appendChild(emptyRow);
            return;
        }

        items.forEach((item, index) => {
            const row = document.createElement('tr');
            if (isPartially) row.setAttribute('data-row-index', index);
            const unitPrice = formatCurrency(item.unitPrice || 0);
            const discount = formatCurrency(item.itemCashDiscount || 0);
            const totalPrice = formatCurrency(item.totalPrice || 0);
            const statusRetur = (item.return_detail_status || 'NOT_RETURNED');
            const statusLabel = statusRetur === 'RETURNED' ? 'RETURNED' : 'NOT_RETURNED';

            let statusCell = '';
            if (isPartially) {
                statusCell = '<td class="border border-gray-400 px-2 py-3 align-top status-retur-cell" data-row-index="' + index + '">' + statusLabel + '</td>';
            }

            row.innerHTML = '<td class="border border-gray-400 px-2 py-3 text-left align-top">≡</td>' +
                '<td class="border border-gray-400 px-2 py-3 text-left align-top">' + (item.item && item.item.name ? item.item.name : 'N/A') + '</td>' +
                '<td class="border border-gray-400 px-2 py-3 align-top">' + (item.item && item.item.no ? item.item.no : 'N/A') + '</td>' +
                '<td class="border border-gray-400 px-2 py-3 align-top">' + (item.quantity || 0) + '</td>' +
                '<td class="border border-gray-400 px-2 py-3 align-top">' + (item.itemUnit && item.itemUnit.name ? item.itemUnit.name : 'N/A') + '</td>' +
                '<td class="border border-gray-400 px-2 py-3 align-top">' + unitPrice + '</td>' +
                '<td class="border border-gray-400 px-2 py-3 align-top">' + discount + '</td>' +
                '<td class="border border-gray-400 px-2 py-3 align-top">' + totalPrice + '</td>' + statusCell;

            if (isPartially) {
                row.style.cursor = 'pointer';
                row.title = 'Double klik untuk mengatur status pengembalian';
                row.addEventListener('dblclick', function() {
                    openReturnDetailStatusModal(index);
                });
            }
            tableBody.appendChild(row);
        });
    }

    function openReturnDetailStatusModal(rowIndex) {
        if (rowIndex < 0 || !detailItems[rowIndex]) return;
        const item = detailItems[rowIndex];
        const name = (item.item && item.item.name) ? item.item.name : (item.item && item.item.no) ? item.item.no : 'Barang #' + (rowIndex + 1);
        const modal = document.getElementById('modalReturnDetailStatus');
        const modalItemName = document.getElementById('modalItemName');
        const selectEl = document.getElementById('modalReturnDetailStatusSelect');
        if (!modal || !modalItemName || !selectEl) return;
        modalItemName.textContent = name;
        selectEl.value = item.return_detail_status || 'NOT_RETURNED';
        modal.setAttribute('data-editing-row', rowIndex);
        if (typeof $ !== 'undefined' && $(modal).modal) $(modal).modal('show');
    }

    function saveReturnDetailStatusFromModal() {
        const modal = document.getElementById('modalReturnDetailStatus');
        const rowIndex = modal ? parseInt(modal.getAttribute('data-editing-row'), 10) : -1;
        const selectEl = document.getElementById('modalReturnDetailStatusSelect');
        if (isNaN(rowIndex) || rowIndex < 0 || !detailItems[rowIndex] || !selectEl) return;
        const value = selectEl.value;
        detailItems[rowIndex].return_detail_status = value;
        const cell = document.querySelector('.status-retur-cell[data-row-index="' + rowIndex + '"]');
        if (cell) cell.textContent = value;
        if (typeof $ !== 'undefined' && $(modal).modal) $(modal).modal('hide');
    }

    // Tombol Lanjut: muat detail barang dari referensi
    const btnLanjut = document.getElementById('btn-lanjut');
    if (btnLanjut) {
        btnLanjut.addEventListener('click', function() {
            const pelangganIdHidden = document.getElementById('pelanggan_id_hidden');
            const returnRefId = document.getElementById('return_referensi_id');
            const returnType = document.getElementById('return_type');
            if (!pelangganIdHidden || !pelangganIdHidden.value.trim()) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Pilih Pelanggan terlebih dahulu.' });
                else alert('Pilih Pelanggan terlebih dahulu.');
                return;
            }
            if (!returnRefId || !returnRefId.value.trim()) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Pilih referensi Retur Dari (Faktur/Pengiriman) terlebih dahulu.' });
                else alert('Pilih referensi Retur Dari terlebih dahulu.');
                return;
            }
            const urlEl = document.getElementById('url_referensi_detail');
            const url = (urlEl && urlEl.value) ? urlEl.value : '';
            if (!url) return;
            const params = new URLSearchParams({ return_type: returnType.value, number: returnRefId.value.trim() });
            btnLanjut.disabled = true;
            btnLanjut.textContent = 'Loading...';
            fetch(url + '?' + params.toString(), { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(function(data) {
                    if (data.success && data.detailItems && data.detailItems.length > 0) {
                        detailItems = data.detailItems;
                        if (getReturnStatusType() === 'partially_returned') {
                            detailItems.forEach(function(it) { it.return_detail_status = it.return_detail_status || 'NOT_RETURNED'; });
                        }
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

    // Saat return_status_type berubah, jika sudah ada detailItems re-render tabel
    const returnStatusTypeEl = document.getElementById('return_status_type');
    if (returnStatusTypeEl) {
        returnStatusTypeEl.addEventListener('change', function() {
            if (getReturnStatusType() === 'partially_returned' && detailItems.length > 0) {
                detailItems.forEach(function(it) { it.return_detail_status = it.return_detail_status || 'NOT_RETURNED'; });
            }
            fillTableWithDetailItems(detailItems);
        });
    }

    // Modal Simpan status
    const modalSaveBtn = document.getElementById('modalReturnDetailStatusSave');
    if (modalSaveBtn) modalSaveBtn.addEventListener('click', saveReturnDetailStatusFromModal);

    function formatDetailItemsForSubmission(items) {
        if (!items || items.length === 0) return [];
        const isPartially = getReturnStatusType() === 'partially_returned';
        return items.map(function(item) {
            const obj = {
                kode: (item.item && item.item.no) ? item.item.no : '',
                kuantitas: item.quantity != null ? item.quantity : 0,
                harga: item.unitPrice !== undefined && item.unitPrice !== null ? item.unitPrice : 0,
                diskon: item.itemCashDiscount !== undefined && item.itemCashDiscount !== null ? item.itemCashDiscount : 0
            };
            if (isPartially) obj.return_detail_status = item.return_detail_status || 'NOT_RETURNED';
            return obj;
        });
    }

    // Tombol Save Retur Penjualan
    const btnSaveRetur = document.getElementById('btn-save-retur-penjualan');
    if (btnSaveRetur) {
        btnSaveRetur.addEventListener('click', function() {
            const form = document.getElementById('returPenjualanForm');
            const pelangganIdHidden = document.getElementById('pelanggan_id_hidden');
            const returnRefId = document.getElementById('return_referensi_id');
            const returnType = document.getElementById('return_type');
            if (!pelangganIdHidden || !pelangganIdHidden.value.trim()) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Validasi', text: 'Pelanggan wajib dipilih.' });
                else alert('Pelanggan wajib dipilih.');
                return;
            }
            if (returnType.value !== 'no_invoice' && (!returnRefId || !returnRefId.value.trim())) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Validasi', text: 'Referensi Retur Dari wajib dipilih.' });
                else alert('Referensi Retur Dari wajib dipilih.');
                return;
            }
            if (!detailItems || detailItems.length === 0) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Validasi', text: 'Klik Lanjut untuk memuat barang terlebih dahulu.' });
                else alert('Klik Lanjut untuk memuat barang terlebih dahulu.');
                return;
            }
            if (getReturnStatusType() === 'partially_returned') {
                const missing = detailItems.some(function(it) { return !it.return_detail_status; });
                if (missing) {
                    if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Validasi', text: 'Untuk Partially Returned, atur status pengembalian setiap barang (double klik baris).' });
                    else alert('Atur status pengembalian setiap barang (double klik baris).');
                    return;
                }
            }
            // Set referensi id ke input yang sesuai
            const pengirimanIdEl = document.getElementById('pengiriman_pesanan_id');
            const fakturIdEl = document.getElementById('faktur_penjualan_id');
            const refVal = returnRefId.value.trim();
            if (returnType.value === 'delivery' && pengirimanIdEl) pengirimanIdEl.value = refVal;
            if ((returnType.value === 'invoice' || returnType.value === 'invoice_dp') && fakturIdEl) fakturIdEl.value = refVal;

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
                if (getReturnStatusType() === 'partially_returned' && item.return_detail_status) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'detailItems[' + index + '][return_detail_status]';
                    input.value = item.return_detail_status;
                    form.appendChild(input);
                }
            });
            document.getElementById('form_submitted').value = '1';
            form.submit();
        });
    }

    // Inisialisasi: tampilkan/sembunyikan referensi sesuai tipe default
    document.addEventListener('DOMContentLoaded', function() {
        toggleReferensiVisibility();
        if (typeof $ !== 'undefined' && $('[data-toggle="tooltip"]').length) $('[data-toggle="tooltip"]').tooltip();
    });
})();
</script>
@endsection