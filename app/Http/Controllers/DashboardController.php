<?php

namespace App\Http\Controllers;

use App\Models\ApprovalStock;
use App\Models\BarangMasuk;
use App\Models\KasirPenjualan;
use App\Models\PackingList;
use App\Models\PenerimaanBarang;
use App\Models\HasilStockOpname;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $activeBranchId = session('active_branch');

        if (!$activeBranchId) {
            return redirect()->route('branch.select')->with('error', 'Silakan pilih cabang dulu.');
        }

        return view('dashboard', compact('activeBranchId'));
    }
}
