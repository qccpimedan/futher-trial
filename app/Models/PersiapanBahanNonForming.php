<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PersiapanBahanNonForming extends Model
{
    protected $table = 'persiapan_bahan_non_forming';

    protected $fillable = [
        'uuid',
        'plan_id',
        'user_id',
        'id_no_formula_non_forming',
        'shift_id',
        'tanggal',
        'jam',
        'id_suhu_adonan',
        'kode_produksi',
        'kode_produksi_emulsi_oil',
        'waktu_mulai_mixing',
        'waktu_selesai_mixing',
        'kondisi',
        'rework',
        'catatan',
        'kode_form',
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'kode_produksi_emulsi_oil' => 'array',
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
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    public function formulaNonForming()
    {
        return $this->belongsTo(MasterProdukNonForming::class, 'id_no_formula_non_forming');
    }

    public function suhuAdonan()
    {
        return $this->belongsTo(SuhuAdonan::class, 'id_suhu_adonan');
    }

    public function details()
    {
        return $this->hasMany(PersiapanBahanNonFormingDetail::class, 'id_persiapan_bahan_non_forming');
    }

    public function aktuals()
    {
        return $this->morphMany(AktualSuhuAdonan::class, 'owner');
    }

    public function aktualSuhuAdonan()
    {
        return $this->morphOne(AktualSuhuAdonan::class, 'owner');
    }
}
