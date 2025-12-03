@extends('layout.main')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Barcode</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Barcode</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- Add Button -->
                        <div class="d-flex justify-content-end mb-3">
                            <form action="{{ route('barcode.updateFromCSV') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-sync-alt"></i> Update
                                </button>
                            </form>
                        </div>

                        <!-- Card Table -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Data Barcode</h3>
                                @if ($lastUpdated)
                                    <span class="badge badge-danger ml-auto"
                                        style="font-size: 0.95rem; min-width:220px; text-align:right; white-space:nowrap;">
                                        Terakhir update:
                                        {{ \Carbon\Carbon::parse($lastUpdated)->locale('id')->isoFormat('dddd, DD-MM-YYYY HH:mm') }}
                                    </span>
                                @endif
                            </div>

                            <div class="card-body">
                                <table id="barcode" class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Barcode</th>
                                            <th>No. Packing List</th>
                                            <th>No. Billing</th>
                                            <th>Kode Barang</th>
                                            <th>Keterangan</th>
                                            <th>Nomor Seri</th>
                                            <th>Pcs</th>
                                            <th>Berat (KG)</th>
                                            <th>Panjang (MLC)</th>
                                            <th>Warna</th>
                                            <th>Bale</th>
                                            <th>Harga PPN</th>
                                            <th>Harga Jual</th>
                                            <th>Pemasok</th>
                                            <th>Customer</th>
                                            <th>Kontrak</th>
                                            <th>Subtotal</th>
                                            <th>Tanggal</th>
                                            <th>Jatuh Tempo</th>
                                            <th>Mobil / No. Polisi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($barcodes as $barcode)
                                            <tr>
                                                <td>{{ $barcode->barcode }}</td>
                                                <td>{{ $barcode->no_packing_list }}</td>
                                                <td>{{ $barcode->no_billing }}</td>
                                                <td>{{ $barcode->kode_barang }}</td>
                                                <td>{{ $barcode->keterangan }}</td>
                                                <td>{{ $barcode->nomor_seri }}</td>
                                                <td>{{ $barcode->pcs }}</td>
                                                <td>{{ number_format($barcode->berat_kg, 2) }}
                                                <td>{{ number_format($barcode->panjang_mlc, 2) }}
                                                <td>{{ $barcode->warna }}</td>
                                                <td>{{ $barcode->bale }}</td>
                                                <td>{{ 'Rp. ' . number_format($barcode->harga_ppn, 0, ',', '.') }}</td>
                                                <td>{{ 'Rp. ' . number_format($barcode->harga_jual, 0, ',', '.') }}</td>
                                                <td>{{ $barcode->pemasok }}</td>
                                                <td>{{ $barcode->customer }}</td>
                                                <td>{{ $barcode->kontrak }}</td>
                                                <td>{{ 'Rp. ' . number_format($barcode->subtotal, 0, ',', '.') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($barcode->tanggal)->format('d-m-Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($barcode->jatuh)->format('d-m-Y') }}</td>
                                                <td>{{ $barcode->no_vehicle }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function updateTitle(pageTitle) {
            document.title = pageTitle;
        }

        updateTitle('Barcode');
    </script>
@endsection
