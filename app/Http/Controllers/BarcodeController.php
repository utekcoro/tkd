<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BarcodeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'owner') {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        $activeBranchId = session('active_branch');
        if (!$activeBranchId) {
            return back()->with('error', 'Cabang belum dipilih.');
        }

        $branch = Branch::find($activeBranchId);
        if (!$branch) {
            return back()->with('error', 'Cabang tidak valid.');
        }

        $barcodes = Barcode::where('kode_customer', $branch->customer_id)->get();
        $lastUpdated = $barcodes->max('updated_at');

        return view('barcode.index', compact('barcodes', 'lastUpdated'));
    }


    public function updateFromCSV()
    {
        $path = storage_path('app/sftp-uploads/EXPORT_BARCODE_TAKA.txt');

        if (!file_exists($path)) {
            return redirect()->route('barcode.index')->with('error', 'File TXT tidak ditemukan.');
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!$lines || count($lines) <= 1) {
            return back()->with('error', 'File TXT kosong atau tidak valid.');
        }

        $header = str_getcsv(array_shift($lines), ';');
        $totalUpdated = 0;

        $user = Auth::user();

        if ($user->role === 'owner') {
            abort(403, 'Anda tidak memiliki hak akses untuk update data.');
        }

        $activeBranchId = session('active_branch');
        if (!$activeBranchId) {
            return back()->with('error', 'Cabang belum dipilih.');
        }

        $branch = Branch::find($activeBranchId);
        if (!$branch) {
            return back()->with('error', 'Cabang tidak valid.');
        }

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $data = str_getcsv($line, ';');

            $get = function ($key) use ($header, $data) {
                $idx = array_search($key, $header);
                return $idx !== false && isset($data[$idx]) ? trim($data[$idx]) : null;
            };

            $kodeCustomer = $get('KODE_CUSTOMER');

            if ($kodeCustomer !== $branch->customer_id) {
                continue;
            }

            $barcodeData = [
                'barcode'         => $get('BARCODE'),
                'no_packing_list' => $get('NO_PACKING_LIST'),
                'no_billing'      => $get('BILLING'),
                'kode_barang'     => $get('SALESTEXT') . '*#' . $get('WARNA') . ' PL:' . $get('NO_PACKING_LIST'),
                'keterangan'      => $get('SALESTEXT') . ' ' . $get('JOBORDER') . '/*#' . $get('WARNA'),
                'nomor_seri'      => $get('BATCHNO'),
                'pcs'             => (int) $get('PCS'),
                'berat_kg'        => (float) str_replace(',', '.', $get('WEIGHT')),
                'panjang_mlc'     => round((float) str_replace(',', '.', $get('LENGTH')) * 0.9, 3),
                'warna'           => $get('WARNA'),
                'bale'            => round((float) str_replace(',', '.', $get('WEIGHT')) / 181.44, 3),
                'harga_ppn'       => (int) str_replace('.', '', explode(',', $get('HARGA_PPN'))[0] ?? 0),
                'harga_jual'      => (int) str_replace('.', '', explode(',', $get('HARGA_JUAL'))[0] ?? 0),
                'pemasok'         => $get('PEMASOK'),
                'customer'        => $get('CUSTOMER'),
                'kontrak'         => $get('CONTRACT'),
                'subtotal'        => (int) str_replace('.', '', explode(',', $get('SUB_TOTAL'))[0] ?? 0),
                'tanggal'         => $get('DATE') ? Carbon::createFromFormat('d.m.Y', $get('DATE'))->format('Y-m-d') : null,
                'jatuh'           => $get('JATUH_TEMPO') ? Carbon::createFromFormat('d.m.Y', $get('JATUH_TEMPO'))->format('Y-m-d') : null,
                'no_vehicle'      => $get('VEHICLE_NUMBER'),
                'kode_customer'   => $kodeCustomer,
            ];

            $barcode = Barcode::updateOrCreate(
                [
                    'barcode'       => $barcodeData['barcode'],
                    'kode_customer' => $barcodeData['kode_customer'],
                ],
                $barcodeData
            );

            if ($barcode->wasRecentlyCreated) {
                $totalUpdated++;
            }
        }

        if ($totalUpdated > 0) {
            return redirect()->route('barcode.index')->with('success', "$totalUpdated data berhasil diperbarui.");
        } else {
            return redirect()->route('barcode.index')->with('info', 'Tidak ada data yang diperbarui.');
        }
    }
}
