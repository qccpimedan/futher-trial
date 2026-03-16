<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PengemasanProduk extends Model
{
    use HasFactory;

    protected $table = 'pengemasan_produk';

    protected $fillable = [
        'uuid',
        'id_plan',
        'id_produk',
        'id_shift',
        'tanggal',
         'jam',
        'user_id',
        'kode_form',
        'berat',
        'tanggal_expired',
        'kode_produksi',
        'std_suhu_produk_iqf',
        'aktual_suhu_produk',
        'waktu_awal_packing',
        'waktu_selesai_packing',
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'tanggal_expired' => 'datetime',
        'aktual_suhu_produk' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }
  public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift');
    }

    public function pengemasanPlastik()
    {
        return $this->hasOne(PengemasanPlastik::class, 'id_pengemasan_produk');
    }
}
