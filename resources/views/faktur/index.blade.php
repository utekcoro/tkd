@extends('layout.main')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Faktur</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Faktur</li>
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
                                <h3 class="card-title">Data Faktur</h3>
                            </div>

                            <div class="card-body">
                                <table id="faktur" class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No. Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fakturs as $faktur)
                                            <tr>
                                                <td><a
                                                        href="{{ route('faktur.show', $faktur->no_billing) }}">{{ $faktur->no_billing }}</a>
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

        updateTitle('Faktur');
    </script>
@endsection
