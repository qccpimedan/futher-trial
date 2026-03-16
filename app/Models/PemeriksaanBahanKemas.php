<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PemeriksaanBahanKemas extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_bahan_kemas';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'tanggal',
        'jam',
        'nama_kemasan',
        'kode_produksi',
        'kondisi_bahan_kemasan',
        'keterangan',
        'kode_form',
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
        'tanggal' => 'datetime',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
