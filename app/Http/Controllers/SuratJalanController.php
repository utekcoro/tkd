<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuratJalanController extends Controller
{
    public function index()
    {
        $branchId = session('active_branch_id');

        $surat_jalans = Barcode::forBranch($branchId)
            ->select('no_billing')
            ->whereNotNull('no_billing')
            ->distinct()
            ->orderByDesc('no_billing')
            ->get();

        return view('surat_jalan.index', compact('surat_jalans'));
    }

    public function show($no_billing)
    {
        $branchId = session('active_branch_id');

        $surat_jalans = Barcode::forBranch($branchId)
            ->where('no_billing', $no_billing)
            ->select(
                'kode_barang',
                'warna',
                DB::raw('SUM(bale) as total_bale'),
                DB::raw('COUNT(*) as total_pcs'),
                DB::raw('SUM(berat_kg) as total_kg'),
                DB::raw('SUM(panjang_mlc) as total_kuantitas')
            )
            ->groupBy('kode_barang', 'warna')
            ->get();

        return view('surat_jalan.detail', compact('surat_jalans', 'no_billing'));
    }
}
