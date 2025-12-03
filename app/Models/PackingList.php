<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingList extends Model
{
    use HasFactory;

    protected $table = 'packing_list';

    protected $fillable = [
        'tanggal',
        'npl',
        'kode_customer',
    ];

    public function barcodes()
    {
        return $this->hasMany(Barcode::class, 'no_packing_list', 'npl');
    }


    public function scopeForBranch($query, $branchId)
    {
        if ($branchId) {
            $branch = \App\Models\Branch::find($branchId);
            if ($branch) {
                return $query->where('kode_customer', $branch->customer_id);
            }
        }
        return $query;
    }
}
