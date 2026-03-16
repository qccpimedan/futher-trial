<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProsesBattering extends Model
{
    protected $table = 'proses_battering';
    protected $fillable = [
        'uuid', 'id_plan', 'user_id', 'penggorengan_uuid', 'predust_uuid', 'id_produk', 'kode_produksi_better', 'id_jenis_better', 'tanggal', 'jam', 'hasil_better'
    ];
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
       date_default_timezone_set('Asia/Jakarta');
    }
    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function plan() { return $this->belongsTo(Plan::class, 'id_plan'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function produk() { return $this->belongsTo(JenisProduk::class, 'id_produk'); }
    public function jenis_better() { return $this->belongsTo(JenisBetter::class, 'id_jenis_better'); }
    public function penggorengan()
    {
        return $this->belongsTo(Penggorengan::class, 'penggorengan_uuid', 'uuid');
    }

    public function predust()
    {
        return $this->belongsTo(PembuatanPredust::class, 'predust_uuid', 'uuid');
    }
}