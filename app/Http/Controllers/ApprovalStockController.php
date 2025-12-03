<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use App\Models\ApprovalStock;
use App\Models\BarangMasuk;
use App\Models\PackingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalStockController extends Controller
{
    public function index(Request $request)
    {
        $approvalStocks = ApprovalStock::where('panjang', '>', 0)->get();
        return view('approval_stock.index', compact('approvalStocks'));
    }

    public function updateFromBarcodes()
    {
        // Fetch all BarangMasuk records
        $barangMasuks = BarangMasuk::all();

        foreach ($barangMasuks as $barangMasuk) {
            // Find the barcode associated with the nbrg
            $barcode = Barcode::where('barcode', $barangMasuk->nbrg)->first();

            // Cek apakah sudah ada approval stock dengan status uploaded
            $existingApproval = ApprovalStock::where('barcode', $barangMasuk->nbrg)->first();
            if ($existingApproval && $existingApproval->status === ApprovalStock::STATUS_UPLOADED) {
                // Skip update jika status uploaded
                continue;
            }

            // Check if the barcode exists
            if ($barcode) {
                // If barcode exists, get the no_packing_list
                $npl = $barcode->no_packing_list;
                // Check if the packing list exists
                $packingListExists = PackingList::where('npl', $npl)->exists();

                // Extract and format the name from keterangan
                $keterangan = $barcode->keterangan;
                $nama = $this->formatKeterangan($keterangan);

                // Create or update the ApprovalStock record with status based on packing list existence
                ApprovalStock::updateOrCreate(
                    ['barcode' => $barangMasuk->nbrg],
                    [
                        'nama' => $nama,
                        'npl' => $npl,
                        'no_invoice' => $barcode->no_billing,
                        'kontrak' => $barcode->kontrak,
                        'panjang' => $barcode->panjang_mlc,
                        'harga_unit' => $barcode->harga_jual,
                        'status' => $packingListExists ? ApprovalStock::STATUS_APPROVED : ApprovalStock::STATUS_DRAFT,
                    ]
                );
            } else {
                // If barcode does not exist, create an ApprovalStock with status draft
                ApprovalStock::updateOrCreate(
                    ['barcode' => $barangMasuk->nbrg],
                    [
                        'nama' => null, // or any default value
                        'npl' => null, // or any default value
                        'no_invoice' => null, // or any default value
                        'kontrak' => null, // or any default value
                        'panjang' => null,
                        'harga_unit' => null,
                        'status' => ApprovalStock::STATUS_DRAFT,
                    ]
                );
            }
        }

        // Redirect back to the index with a success message
        return redirect()->route('approval_stock.index')->with('success', 'Approval stock updated successfully.');
    }

    public function formatKeterangan($keterangan)
    {
        // Bersihkan karakter kontrol termasuk \x1A (SUB character)
        $cleaned = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $keterangan);
        $cleaned = trim($cleaned);
    
        // Split berdasarkan slash
        $parts = explode('/', $cleaned);
    
        if (count($parts) >= 4) {
            $nama = trim($parts[0]);
    
            // Ambil bagian ke-4 (indeks 3) yang berisi detail
            $detailPart = trim($parts[3]);
    
            // Hapus karakter * dan karakter kontrol lainnya
            $detailPart = str_replace('*', '', $detailPart);
            $detailPart = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $detailPart);
    
            // Ekstrak nomor dan warna dari detailPart
            $extractedData = $this->extractNumberAndColor($detailPart);
            $nomor = $extractedData['number'];
            $warna = $extractedData['color'];
    
            // Jika tidak ada warna di detailPart, coba ambil dari bagian ke-5
            if (empty($warna) && count($parts) >= 5) {
                $warna = trim($parts[4]);
                $warna = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $warna);
            }
    
            return $nama . ' #' . $nomor . ($warna ? ' ' . $warna : '');
        }
    
        return $cleaned;
    }
    
    private function extractNumberAndColor($detailPart)
    {
        $result = [
            'number' => '000',
            'color' => ''
        ];
    
        // Pattern 1: #012/NAVY (dengan slash)
        if (preg_match('/#(\d+)\/([A-Z\s]+)$/i', $detailPart, $matches)) {
            $result['number'] = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
            $result['color'] = trim($matches[2]);
            return $result;
        }
    
        // Pattern 2: #018 HITAM (dengan spasi)
        if (preg_match('/#(\d+)\s+([A-Z\s]+)$/i', $detailPart, $matches)) {
            $result['number'] = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
            $result['color'] = trim($matches[2]);
            return $result;
        }
    
        // Pattern 3: #033T (angka diikuti huruf tanpa spasi)
        if (preg_match('/#(\d+)([A-Z]+)$/i', $detailPart, $matches)) {
            $result['number'] = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
            $result['color'] = trim($matches[2]);
            return $result;
        }
    
        // Pattern 4: #HITAM (hanya warna tanpa angka)
        if (preg_match('/#([A-Z\s]+)$/i', $detailPart, $matches)) {
            $result['number'] = '000';
            $result['color'] = trim($matches[1]);
            return $result;
        }
    
        // Pattern 5: #012 (hanya angka)
        if (preg_match('/#(\d+)$/', $detailPart, $matches)) {
            $result['number'] = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
            return $result;
        }
    
        // Fallback: cari angka saja
        if (preg_match('/(\d+)/', $detailPart, $matches)) {
            $result['number'] = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
        }
    
        // Coba ekstrak huruf sebagai warna (jika ada huruf setelah angka)
        if (preg_match('/\d+([A-Z\s]+)$/i', $detailPart, $matches)) {
            $result['color'] = trim($matches[1]);
        }
    
        return $result;
    }
}
