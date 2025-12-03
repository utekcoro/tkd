@extends('layout.main')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Surat Jalan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Surat Jalan</li>
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
                            <div class="card-header">
                                <h3 class="card-title">Data Surat Jalan</h3>
                            </div>

                            <div class="card-body">
                                <table id="surat_jalan" class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No. Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($surat_jalans as $surat_jalan)
                                            <tr>
                                                <td><a
                                                        href="{{ route('surat_jalan.show', $surat_jalan->no_billing) }}">{{ $surat_jalan->no_billing }}</a>
                                                </td>
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

        updateTitle('Surat Jalan');
    </script>
@endsection
