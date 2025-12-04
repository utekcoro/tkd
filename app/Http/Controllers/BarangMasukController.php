<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangMasukController extends Controller
{
    public function index()
    {
        $activeBranchId = session('active_branch');
        if (!$activeBranchId) {
            return back()->with('error', 'Cabang belum dipilih.');
        }

        $branch = Branch::find($activeBranchId);
        if (!$branch) {
            return back()->with('error', 'Cabang tidak valid.');
        }

        $branch = Branch::find(session('active_branch'));

        $barangMasuk = BarangMasuk::with(['barcode' => function ($query) use ($branch) {
            if ($branch) {
                $query->where('kode_customer', $branch->customer_id);
            }
        }])
            ->forBranch()
            ->orderByDesc('tanggal')
            ->get();

        return view('barang_masuk.index', compact('barangMasuk', 'branch'));
    }

    public function create()
    {
        return view('barang_masuk.create');
    }

    public function store(Request $request)
    {
        $activeBranchId = session('active_branch');
        if (!$activeBranchId) {
            return back()->with('error', 'Cabang belum dipilih.');
        }

        $branch = Branch::find($activeBranchId);
        if (!$branch) {
            return back()->with('error', 'Cabang tidak valid.');
        }

        // Ambil hanya 10 karakter pertama dari barcode
        if ($request->filled('nbrg')) {
            $barcode = explode(';', $request->input('nbrg'))[0];
            $request->merge(['nbrg' => substr($barcode, 0, 10)]);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nbrg' => 'required|string|max:20|unique:barang_masuk,nbrg',
        ], [
            'nbrg.unique' => 'No. Barang tersebut sudah terinput.',
        ]);

        // Simpan data dengan kode_customer cabang aktif
        BarangMasuk::create([
            'tanggal' => $validated['tanggal'],
            'nbrg' => $validated['nbrg'],
            'kode_customer' => $branch->customer_id,
        ]);

        return redirect()->route('barang-masuk.create')->with('success', 'Barang Masuk berhasil ditambahkan');
    }

    public function edit(BarangMasuk $barangMasuk)
    {
        return view('barang_masuk.edit', compact('barangMasuk'));
    }

    public function update(Request $request, $id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nbrg' => 'required|string|max:20',
        ]);

        $barangMasuk->update($validated);

        return redirect()->route('barang-masuk.index')->with('success', 'Barang Masuk berhasil diperbarui');
    }

    public function destroy(BarangMasuk $barangMasuk)
    {
        $barangMasuk->delete();
        return redirect()->route('barang-masuk.index')->with('success', 'Barang Masuk berhasil dihapus');
    }
}
