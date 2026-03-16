<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PersiapanBahanForming extends Model
{
    protected $table = 'persiapan_bahan_forming';
    protected $fillable = [
        'uuid', 'id_formula', 'kode_produksi_emulsi', 'kode_produksi_emulsi_oil', 'kondisi', 'suhu_rm', 'rework', 'catatan', 'plan_id', 'user_id', 'shift_id', 'tanggal', 'jam', 'kode_form',
        // new fields merged from Cold Mixing
        'id_suhu_adonan', 'waktu_mulai_mixing', 'waktu_selesai_mixing',
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
        // Pastikan waktu tidak terpotong (00:00:00). Simpan & serialize sebagai datetime lengkap.
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'kode_produksi_emulsi_oil' => 'array', // Cast ke array untuk dynamic form

    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'plan_id');
    }
    
    public function produk() { 
        return $this->belongsTo(JenisProduk::class, 'id_produk'); 
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function bahanForming()
    {
        return $this->belongsTo(\App\Models\BahanForming::class, 'id_bahan_forming');
    }
    
    public function formula()
    {
        return $this->belongsTo(\App\Models\NomorFormula::class, 'id_formula');
    }

    public function suhuForming()
    {
        return $this->hasMany(\App\Models\SuhuForming::class, 'id_persiapan_bahan_forming');
    }
    public function shift()
    {
        return $this->belongsTo(\App\Models\DataShift::class, 'shift_id');
    }
    // new relations
    public function suhuAdonan()
    {
        return $this->belongsTo(\App\Models\SuhuAdonan::class, 'id_suhu_adonan');
    }

    public function aktuals()
    {
        return $this->morphMany(\App\Models\AktualSuhuAdonan::class, 'owner');
    }

    // Alias to support eager load key used in controller
    public function aktualSuhuAdonan()
    {
        return $this->morphOne(\App\Models\AktualSuhuAdonan::class, 'owner');
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
