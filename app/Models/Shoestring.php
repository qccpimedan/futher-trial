<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Shoestring extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'id_plan', 'created_by', 'nama_produsen', 'kode_produksi', 'best_before', 'sampling_defect', 'sampling_defect_qty', 'total_defect', 'catatan', 'shift_id', 'tanggal', 'jam', 'kode_form', 'dokumentasi',
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
        'sampling_defect_qty' => 'array',
        'dokumentasi' => 'array',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function shift()
    {
        return $this->belongsTo(\App\Models\DataShift::class, 'shift_id');
    }

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'id_plan');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Relasi ke User yang melakukan approval
    public function qcApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'qc_approved_by');
    }

    public function produksiApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'produksi_approved_by');
    }

    public function spvApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'spv_approved_by');
    }
}
