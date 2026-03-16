<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProsesAging extends Model
{
    use HasFactory;

    protected $table = 'proses_aging';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'id_produk',
        'proses_tumbling_id',
        'proses_tumbling_uuid',
        'waktu_mulai_aging',
        'waktu_selesai_aging',
        'suhu_produk',
        'kondisi_produk',
        'jam',
        'tanggal',
        'kode_form',
        'approved_by_qc',
        'approved_by_spv',
        'approved_by_produksi',
        'qc_approved_by',
        'spv_approved_by',
        'produksi_approved_by',
        'qc_approved_at',
        'spv_approved_at',
        'produksi_approved_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'tanggal' => 'date',
        'approved_by_qc' => 'boolean',
        'approved_by_spv' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'qc_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
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

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relationships
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function prosesTumbling()
    {
        return $this->belongsTo(ProsesTumbling::class, 'proses_tumbling_id');
    }

    public function qcApprover()
    {
        return $this->belongsTo(User::class, 'qc_approved_by');
    }

    public function spvApprover()
    {
        return $this->belongsTo(User::class, 'spv_approved_by');
    }

    public function produksiApprover()
    {
        return $this->belongsTo(User::class, 'produksi_approved_by');
    }
}