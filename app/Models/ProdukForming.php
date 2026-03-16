<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProdukForming extends Model
{
    use HasFactory;

    protected $table = 'produk_forming';

    protected $fillable = [
        'uuid',
        'kode_form',
        'id_produk',
        'id_plan',
        'id_shift',
        'user_id',
        'tanggal',
        'jam',
        'bahan_baku',
        'bahan_penunjang',
        'kemasan_plastik',
        'kemasan_karton',
        'labelisasi_plastik',
        'labelisasi_karton',
        'autogrind',
        'bowlcutter',
        'ayakan_seasoning',
        'unimix',
        'revoformer',
        'better_mixer',
        'wet_coater',
        'breader',
        'frayer_1',
        'frayer_2',
        'iqf_jbt',
        'keranjang',
        'timbangan',
        'mhw',
        'foot_sealer',
        'metal_detector',
        'rotary_table',
        'carton_sealer',
        'meatcar',
        'check_weigher_bag',
        'check_weigher_box',
        'penilaian',
        'tindakan_koreksi',
        'verifikasi',
        // Approval fields
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'qc_approved_by',
        'produksi_approved_by',
        'spv_approved_by',
        'qc_approved_at',
        'produksi_approved_at',
        'spv_approved_at'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'bahan_baku' => 'array',
        'bahan_penunjang' => 'array',
        'penilaian' => 'array',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime'
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
    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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

    // Helper methods untuk options
    public static function getBahanOptions()
    {
        return [
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6'
        ];
    }

    public static function getKemasanOptions()
    {
        return [
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6'
        ];
    }

    public static function getLabelisasiOptions()
    {
        return [
            1 => '1',
            2 => '2'
        ];
    }

    public static function getMesinOptions()
    {
        return [
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8'
        ];
    }

    public static function getPenilaianOptions()
    {
        return [
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6'
        ];
    }

    public static function getVerifikasiOptions()
    {
        return [
            '✓' => 'Ok (✓)',
            '✗' => 'Tidak Ok (✗)'
        ];
    }
}
