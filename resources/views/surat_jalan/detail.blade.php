@extends('layout.main')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Detail Surat Jalan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('surat_jalan.index') }}">Surat Jalan</a></li>
                            <li class="breadcrumb-item active">Surat Jalan Detail</li>
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
                                <table id="surat_jalan_detail" class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Kode Barang</th>
                                            <th>Warna</th>
                                            <th>Bale</th>
                                            <th>Pcs</th>
                                            <th>KG</th>
                                            <th>Kuantitas</th>
                                            <th>Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($surat_jalans as $surat_jalan)
                                            <tr>
                                                <td>{{ $surat_jalan->kode_barang }}</td>
                                                <td>{{ $surat_jalan->warna }}</td>
                                                <td>{{ number_format($surat_jalan->total_bale, 2) }}</td>
                                                <td>{{ $surat_jalan->total_pcs }}</td> <!-- Pcs -->
                                                <td>{{ number_format($surat_jalan->total_kg, 3) }}</td>
                                                <td>{{ $surat_jalan->total_kuantitas }}</td> <!-- Kuantitas -->
                                                <td>MLC</td> <!-- Satuan -->
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

        updateTitle('Surat Jalan Detail');
    </script>
@endsection
