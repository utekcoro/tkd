<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FakturController extends Controller
{
    public function index()
    {
        $branchId = session('active_branch_id');

        $fakturs = Barcode::forBranch($branchId)
            ->select('no_billing')
            ->whereNotNull('no_billing')
            ->distinct()
            ->orderByDesc('no_billing')
            ->get();

        return view('faktur.index', compact('fakturs'));
    }

    public function show($no_billing)
    {
        $branchId = session('active_branch_id');

        $fakturs = Barcode::forBranch($branchId)
            ->where('no_billing', $no_billing)
            ->select(
                'kode_barang',
                'warna',
                DB::raw('SUM(bale) as total_bale'),
                DB::raw('COUNT(*) as total_pcs'),
                DB::raw('SUM(berat_kg) as total_kg'),
                DB::raw('SUM(panjang_mlc) as total_kuantitas'),
                DB::raw('MAX(harga_ppn) as harga_ppn'),
                DB::raw('MAX(harga_jual) as harga_jual'),
                DB::raw('SUM(subtotal) as total_subtotal')
            )
            ->groupBy('kode_barang', 'warna')
            ->get();

        return view('faktur.detail', compact('fakturs', 'no_billing'));
    }
}
