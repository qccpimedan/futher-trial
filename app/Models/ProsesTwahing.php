<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\DataShift;
use App\Models\Plan;
use App\Models\ProsesTwahingDetail;
use App\Models\User;

class ProsesTwahing extends Model
{
    use HasFactory;

    protected $table = 'proses_twahing';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'id_shift',
        'tanggal',
        'jam',
        'waktu_thawing_awal',
        'waktu_thawing_akhir',
        'kondisi_kemasan_rm',
        'total_waktu_thawing_jam',
        'catatan',
        'kode_form',
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'approved_by_qc_at',
        'approved_by_produksi_at',
        'approved_by_spv_at',
        'approved_by_qc_user_id',
        'approved_by_produksi_user_id',
        'approved_by_spv_user_id',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'jam' => 'string',
        'total_waktu_thawing_jam' => 'decimal:2',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'approved_by_qc_at' => 'datetime',
        'approved_by_produksi_at' => 'datetime',
        'approved_by_spv_at' => 'datetime',
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

    public function details()
    {
        return $this->hasMany(ProsesTwahingDetail::class, 'proses_twahing_id');
    }
}
