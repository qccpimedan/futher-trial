<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PembekuanIqfRoasting extends Model
{
    use HasFactory;

    protected $table = 'pembekuan_iqf_roasting';

    protected $fillable = [
        'uuid',
        'id_plan',
        'shift_id',
        'user_id',
        'tanggal',
        'jam',
        'suhu_ruang_iqf',
        'holding_time',
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
        // UUID fields untuk relasi ke proses sebelumnya
        'input_roasting_uuid',
        'hasil_proses_roasting_uuid',
        'proses_roasting_fan_uuid',
        'frayer_uuid',
        'frayer2_uuid',  // TAMBAH INI
        'frayer3_uuid',  // TAMBAH INI
        'frayer4_uuid',  // TAMBAH INI
        'frayer5_uuid',  // TAMBAH INI
        'breader_uuid',
        'battering_uuid',
        'predust_uuid',
        'penggorengan_uuid'
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

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        date_default_timezone_set('Asia/Jakarta');
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

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    // Relasi ke proses sebelumnya
    public function inputRoasting()
    {
        return $this->belongsTo(InputRoasting::class, 'input_roasting_uuid', 'uuid');
    }

    public function bahanBakuRoasting()
    {
        return $this->belongsTo(BahanBakuRoasting::class, 'bahan_baku_roasting_uuid', 'uuid');
    }

    public function hasilProsesRoasting()
    {
        return $this->belongsTo(HasilProsesRoasting::class, 'hasil_proses_roasting_uuid', 'uuid');
    }

    public function prosesRoastingFan()
    {
        return $this->belongsTo(ProsesRoastingFan::class, 'proses_roasting_fan_uuid', 'uuid');
    }

    public function frayer()
    {
        return $this->belongsTo(ProsesFrayer::class, 'frayer_uuid', 'uuid');
    }

    public function breader()
    {
        return $this->belongsTo(ProsesBreader::class, 'breader_uuid', 'uuid');
    }

    public function battering()
    {
        return $this->belongsTo(ProsesBattering::class, 'battering_uuid', 'uuid');
    }

    public function predust()
    {
        return $this->belongsTo(PembuatanPredust::class, 'predust_uuid', 'uuid');
    }

    public function penggorengan()
    {
        return $this->belongsTo(Penggorengan::class, 'penggorengan_uuid', 'uuid');
    }

    // Alias relasi untuk compatibility dengan controller
    public function penggorenganData()
    {
        return $this->penggorengan();
    }

    public function inputRoastingData()
    {
        return $this->inputRoasting();
    }

    public function bahanBakuRoastingData()
    {
        return $this->bahanBakuRoasting();
    }

    public function hasilProsesRoastingData()
    {
        return $this->hasilProsesRoasting();
    }

    public function prosesRoastingFanData()
    {
        return $this->prosesRoastingFan();
    }

    public function frayerData()
    {
        return $this->frayer();
    }

    public function frayer2Data()
    {
        return $this->belongsTo(Frayer2::class, 'frayer2_uuid', 'uuid');
    }

    public function frayer3Data()
    {
        return $this->belongsTo(Frayer3::class, 'frayer3_uuid', 'uuid');
    }

    public function frayer4Data()
    {
        return $this->belongsTo(Frayer4::class, 'frayer4_uuid', 'uuid');
    }

    public function frayer5Data()
    {
        return $this->belongsTo(Frayer5::class, 'frayer5_uuid', 'uuid');
    }

    public function breaderData()
    {
        return $this->breader();
    }

    public function batteringData()
    {
        return $this->battering();
    }

    public function predustData()
    {
        return $this->predust();
    }

    // Tambahkan accessor untuk mendapatkan shift dari HasilProsesRoasting atau Penggorengan (Dual Flow)
    public function getShiftDataAttribute()
    {
        // KONDISI 1: Cek shift dari Penggorengan (Alur Penggorengan)
        if ($this->penggorengan_uuid) {
            $penggorengan = \App\Models\Penggorengan::where('uuid', $this->penggorengan_uuid)->first();
            if ($penggorengan && $penggorengan->shift) {
                return $penggorengan->shift;
            }
        }
        
        // KONDISI 2: Cek shift dari HasilProsesRoasting (yang sudah punya dual check)
        if ($this->hasil_proses_roasting_uuid) {
            $hasilRoasting = \App\Models\HasilProsesRoasting::where('uuid', $this->hasil_proses_roasting_uuid)->first();
            if ($hasilRoasting && $hasilRoasting->shift_data) {
                return $hasilRoasting->shift_data;
            }
        }
        
        // Fallback ke relationship shift lokal
        return $this->shift;
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
