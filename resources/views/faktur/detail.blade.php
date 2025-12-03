@extends('layout.main')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Faktur</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('faktur.index') }}">Faktur</a></li>
                            <li class="breadcrumb-item active">Faktur Detail</li>
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

                        <!-- Card Table -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="text-start">
                                    <h3 class="card-title mb-0"><b>No. Invoice: {{ $no_billing }}</b></h3>
                                </div>
                            </div>

                            <div class="card-body">
                                <table id="faktur_detail" class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Kode Barang</th>
                                            <th>Warna</th>
                                            <th>Bale</th>
                                            <th>Pcs</th>
                                            <th>KG</th>
                                            <th>Kuantitas</th>
                                            <th>Satuan</th>
                                            <th>Harga PPN</th>
                                            <th>Harga Jual</th>
                                            <th>Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fakturs as $faktur)
                                            <tr>
                                                <td>{{ $faktur->kode_barang }}</td>
                                                <td>{{ $faktur->warna }}</td>
                                                <td>{{ number_format($faktur->total_bale, 2) }}</td>
                                                <td>{{ $faktur->total_pcs }}</td>
                                                <td>{{ number_format($faktur->total_kg, 2) }}</td>
                                                <td>{{ $faktur->total_kuantitas }}</td> <!-- Kuantitas -->
                                                <td>MLC</td> <!-- Satuan -->
                                                <td>{{ 'Rp. ' . number_format($faktur->harga_ppn, 0, ',', '.') }}</td> <!-- Harga PPN -->
                                                <td>{{ 'Rp. ' . number_format($faktur->harga_jual, 0, ',', '.') }}</td> <!-- Harga Jual -->
                                                <td>{{ 'Rp. ' . number_format($faktur->subtotal, 0, ',', '.') }}</td> <!-- Sub Total -->
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

        updateTitle('Faktur Detail');
    </script>
@endsection
