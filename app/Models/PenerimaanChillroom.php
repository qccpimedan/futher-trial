<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PenerimaanChillroom extends Model
{
    use HasFactory;

    protected $table = 'penerimaan_chillroom';

    protected $fillable = [
        'uuid',
        'nama_rm',
        'kode_produksi',
        'berat',
        'tanggal',
        'suhu',
        'shift_id',
        'jam_kedatangan',
        'sensori',
        'kemasan',
        'keterangan',
        'standar_berat',
        'jumlah_rm',
        'status_rm',
        'nilai_jumlah_rm',
        'catatan_rm',
        'id_plan',
        'user_id',
        'kode_form',
        // Approval fields
        'approved_by_qc',
        'qc_approved_by',
        'qc_approved_at',
        'approved_by_produksi',
        'produksi_approved_by',
        'produksi_approved_at',
        'approved_by_spv',
        'spv_approved_by',
        'spv_approved_at',
    ];
    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'jam_kedatangan' => 'datetime:H:i',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }
    // Relasi ke Plan
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }
       // Relasi ke Shift
    public function datashift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke User yang melakukan approval
    public function qcApprover()
    {
        return $this->belongsTo(User::class, 'qc_approved_by');
    }

    public function produksiApprover()
    {
        return $this->belongsTo(User::class, 'produksi_approved_by');
    }

    public function spvApprover()
    {
        return $this->belongsTo(User::class, 'spv_approved_by');
    }
}