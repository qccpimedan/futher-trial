<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InputMetalDetector extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    protected $table = 'input_metal_detector';
    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
    ];
    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'id_shift',
        'line',
        'id_produk',
        'tanggal',
           'jam',
        'berat_produk',
        'kode_produksi',
        'fe_depan_aktual',
        'fe_tengah_aktual',
        'fe_belakang_aktual',
        'non_fe_depan_aktual',
        'non_fe_tengah_aktual',
        'non_fe_belakang_aktual',
        'sus_depan_aktual',
        'sus_tengah_aktual',
        'sus_belakang_aktual',
        'keterangan',
        'kode_form',
        // Approval fields
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'qc_approved_by',
        'produksi_approved_by',
        'spv_approved_by',
        'qc_approved_at',
        'produksi_approved_at',
        'spv_approved_at',
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function plan()
    { 
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');
    }

    // Approval relationships
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
