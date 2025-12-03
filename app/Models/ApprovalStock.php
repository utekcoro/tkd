<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ApprovalStock extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'approval_stocks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'barcode',
        'nama',
        'npl',
        'no_invoice',
        'kontrak',
        'status',
        'id_pb',
        'panjang',
        'harga_unit'
    ];

    /**
     * The attributes that should be cast.
     *
     * Cast `status` as string (enum is stored as string in DB).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Possible statuses for ApprovalStock.
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_UPLOADED = 'uploaded';

    /**
     * Get all possible statuses.
     *
     * @return string[]
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_APPROVED,
            self::STATUS_UPLOADED,
        ];
    }

    /**
     * Konfigurasi activity log untuk model ApprovalStock
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['barcode', 'nama', 'npl', 'no_invoice', 'kontrak', 'status', 'id_pb', 'panjang', 'harga_unit']) // Log field yang ada di ApprovalStock
            ->logOnlyDirty() // Hanya log perubahan yang benar-benar terjadi
            ->dontSubmitEmptyLogs() // Jangan submit log kosong
            ->useLogName('Pembaruan Data Approval Stock') // Set log name sesuai permintaan
            ->logFillable() // Log semua fillable attributes
            ->logUnguarded(); // Log unguarded attributes juga
    }

    /**
     * Customize what gets logged for created and updated events
     */
    public function tapActivity($activity, string $eventName)
    {
        // Hanya handle event created dan updated (tidak ada deleted)
        if (!in_array($eventName, ['created', 'updated'])) {
            return; // Skip jika bukan event created atau updated
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

        switch ($eventName) {
            case 'created':
                // Untuk created, tampilkan data yang diisi
                $activity->description = "Approval Stock baru dengan barcode '{$this->barcode}' untuk '{$this->nama}' telah dibuat";
                $activity->properties = $activity->properties->merge([
                    'event_type' => 'created',
                    'created_data' => [
                        'barcode' => $this->barcode,
                        'nama' => $this->nama,
                        'npl' => $this->npl,
                        'no_invoice' => $this->no_invoice,
                        'kontrak' => $this->kontrak,
                        'status' => $this->status,
                        'id_pb' => $this->id_pb,
                        'panjang' => $this->panjang,
                        'harga_unit' => $this->harga_unit,
                        'created_at' => $this->created_at->format('Y-m-d H:i:s')
                    ],
                    'causer_info' => $causerInfo,
                    'timestamp_info' => $timestampInfo
                ]);
                break;

            case 'updated':
                // Untuk updated, tampilkan before dan after data
                $changes = $this->getChanges();
                $original = array_intersect_key($this->getOriginal(), $changes);
                
                $activity->description = "Data Approval Stock dengan barcode '{$this->barcode}' telah diupdate";
                $activity->properties = $activity->properties->merge([
                    'event_type' => 'updated',
                    'before_update' => $original,
                    'after_update' => $changes,
                    'updated_fields' => array_keys($changes),
                    'causer_info' => $causerInfo,
                    'timestamp_info' => $timestampInfo
                ]);
                break;
        }
    }
}
