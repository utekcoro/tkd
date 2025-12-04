<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class HasilStockOpnameBarcode extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'hasil_stock_opname_barcode';

    protected $fillable = [
        'nop',
        'barcode',
        'kode_customer',
    ];

    /**
     * Relasi Many-to-One dengan model HasilStockOpname
     * Banyak HasilStockOpnameBarcode belongs to satu HasilStockOpname
     */
    public function hasilStockOpname()
    {
        return $this->belongsTo(HasilStockOpname::class, 'nop', 'nop');
    }

    /**
     * Konfigurasi activity log untuk model HasilStockOpnameBarcode
     * Hanya log event 'created'
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nop', 'barcode', 'kode_customer'])
            ->logOnlyDirty() // Hanya log perubahan yang benar-benar terjadi
            ->dontSubmitEmptyLogs() // Jangan submit log kosong
            ->useLogName('Pembuatan Data Hasil Stock Opname Barcode') // Set log name
            ->logFillable() // Log semua fillable attributes
            ->logUnguarded() // Log unguarded attributes juga
            ->logExcept(['updated_at']); // Exclude updated_at dari log
    }

    /**
     * Customize what gets logged for created event only
     */
    public function tapActivity($activity, string $eventName)
    {
        // Hanya handle event created (tidak ada updated dan deleted)
        if ($eventName !== 'created') {
            return; // Skip jika bukan event created
        }

        // Dapatkan informasi user yang sedang login (causer)
        $causer = Auth::user();
        $causerInfo = null;
        
        if ($causer) {
            $causerInfo = [
                'causer_id' => $causer->id,
                'causer_type' => get_class($causer),
                'causer_name' => $causer->name,
                'causer_username' => $causer->username,
                'causer_role' => $causer->role
            ];
        }

        // Tambahkan informasi waktu yang detail
        $timestampInfo = [
            'action_date' => now()->format('Y-m-d'),
            'action_time' => now()->format('H:i:s'),
            'action_datetime' => now()->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone', 'UTC')
        ];

        // Untuk created, tampilkan data yang diisi
        $activity->description = "Barcode '{$this->barcode}' telah ditambahkan ke Hasil Stock Opname dengan NOP '{$this->nop}'";
        $activity->properties = $activity->properties->merge([
            'event_type' => 'created',
            'created_data' => [
                'nop' => $this->nop,
                'barcode' => $this->barcode,
                'kode_customer' => $this->kode_customer,
                'created_at' => $this->created_at->format('Y-m-d H:i:s')
            ],
            'hasil_stock_opname_info' => [
                'nop' => $this->nop,
                'related_hasil_stock_opname' => $this->hasilStockOpname ? [
                    'nop' => $this->hasilStockOpname->nop,
                    'no_perintah_opname' => $this->hasilStockOpname->no_perintah_opname,
                    'tanggal' => $this->hasilStockOpname->tanggal
                ] : null
            ],
            'causer_info' => $causerInfo,
            'timestamp_info' => $timestampInfo
        ]);
    }
}
