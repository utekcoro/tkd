<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';

    protected $fillable = [
        'tanggal',
        'nbrg',
        'kode_customer',
    ];

    /**
     * Relasi ke Barcode berdasarkan barcode & kode_customer.
     * Gunakan hasOne agar bisa memakai whereColumn dengan aman.
     */
    public function barcode()
    {
        return $this->hasOne(Barcode::class, 'barcode', 'nbrg');
    }


    /**
     * Scope filter berdasarkan cabang aktif.
     */
    public function scopeForBranch($query)
    {
        if (session()->has('active_branch')) {
            $branch = \App\Models\Branch::find(session('active_branch'));
            if ($branch) {
                return $query->where('kode_customer', $branch->customer_id);
            }
        }
        return $query;
    }
}
