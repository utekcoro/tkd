<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;

class Barcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'no_packing_list',
        'no_billing',
        'kode_barang',
        'keterangan',
        'nomor_seri',
        'pcs',
        'berat_kg',
        'panjang_mlc',
        'warna',
        'bale',
        'harga_ppn',
        'harga_jual',
        'pemasok',
        'customer',
        'kontrak',
        'subtotal',
        'tanggal',
        'jatuh',
        'no_vehicle',
        'kode_customer',
    ];

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $branchId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForBranch($query, $branchId = null)
    {
        if (!$branchId) {
            $branchId = session('active_branch') ?? session('active_branch_id') ?? null;
        }

        $branch = null;

        if ($branchId instanceof Branch) {
            $branch = $branchId;
        } elseif ($branchId) {
            $branch = Branch::find($branchId);

            if (!$branch) {
                $branch = Branch::where('customer_id', $branchId)->first();
            }
        }

        if ($branch && !empty($branch->customer_id)) {
            return $query->where('kode_customer', $branch->customer_id);
        }

        if (Auth::check() && optional(Auth::user())->role === 'super_admin') {
            return $query;
        }

        return $query->whereRaw('1=0');
    }
}
