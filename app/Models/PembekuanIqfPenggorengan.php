<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PembekuanIqfPenggorengan extends Model
{
    use HasFactory;

    protected $table = 'pembekuan_iqf_penggorengan';
    
    protected $fillable = [
        'uuid', 'id_plan', 'user_id', 'tanggal', 'suhu_ruang_iqf', 'holding_time',
        'hasil_penggorengan_uuid', 'frayer_uuid', 'frayer2_uuid', 'breader_uuid', 
        'battering_uuid', 'predust_uuid', 'penggorengan_uuid','jam', 'kode_form',
        'approved_by_qc', 'approved_by_produksi', 'approved_by_spv',
        'qc_approved_by', 'produksi_approved_by', 'spv_approved_by',
        'qc_approved_at', 'produksi_approved_at', 'spv_approved_at'
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
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

    // Relationships
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan', 'id');
    }

    public function shift()
    {
        return $this->penggorenganData ? $this->penggorenganData->shift : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function hasilPenggorenganData()
    {
        return $this->belongsTo(HasilPenggorengan::class, 'hasil_penggorengan_uuid', 'uuid');
    }

    public function frayerData()
    {
        return $this->belongsTo(ProsesFrayer::class, 'frayer_uuid', 'uuid');
    }

    public function frayer3Data()
    {
        return $this->belongsTo(Frayer3::class, 'frayer_uuid', 'uuid');
    }

    public function frayer4Data()
    {
        return $this->belongsTo(Frayer4::class, 'frayer_uuid', 'uuid');
    }

    public function frayer5Data()
    {
        return $this->belongsTo(Frayer5::class, 'frayer_uuid', 'uuid');
    }

    public function frayer2Data()
    {
        return $this->belongsTo(Frayer2::class, 'frayer2_uuid', 'uuid');
    }

    public function breaderData()
    {
        return $this->belongsTo(ProsesBreader::class, 'breader_uuid', 'uuid');
    }

    public function batteringData()
    {
        return $this->belongsTo(ProsesBattering::class, 'battering_uuid', 'uuid');
    }

    public function predustData()
    {
        return $this->belongsTo(PembuatanPredust::class, 'predust_uuid', 'uuid');
    }

    public function penggorenganData()
    {
        return $this->belongsTo(Penggorengan::class, 'penggorengan_uuid', 'uuid');
    }

    // Approval relationships
    public function qcApprover()
    {
        return $this->belongsTo(User::class, 'qc_approved_by', 'id');
    }

    public function produksiApprover()
    {
        return $this->belongsTo(User::class, 'produksi_approved_by', 'id');
    }

    public function spvApprover()
    {
        return $this->belongsTo(User::class, 'spv_approved_by', 'id');
    }
}
