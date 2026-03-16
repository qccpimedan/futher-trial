<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dokumentasi extends Model
{
    use HasFactory;
     protected $table = 'dokumentasi';

    protected $fillable = [
        'uuid',
        'id_pengemasan_karton',
        'id_berat_produk_box',
        'id_berat_produk_bag',
        'id_pengemasan_plastik',
        'id_pengemasan_produk',
        'user_id',
        'id_plan',
        'id_shift',
        'foto_kode_produksi',
        'qr_code',
        'label_polyroll',
        'tanggal',
         'jam', 
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
        'tanggal' => 'datetime:Y-m-d H:i:s',
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
            $model->uuid = (string) Str::uuid();
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

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'id_shift', 'id');    
    }

        public function beratProdukBox()
    {
        return $this->belongsTo(BeratProdukBox::class, 'id_berat_produk_box');
    }
    public function beratProdukBag()
    {
        return $this->belongsTo(BeratProdukBag::class, 'id_berat_produk_bag');
    }
    public function pengemasanPlastik()
    {
        return $this->belongsTo(PengemasanPlastik::class, 'id_pengemasan_plastik');
    }
    public function pengemasanProduk()
    {
        return $this->belongsTo(PengemasanProduk::class, 'id_pengemasan_produk');
    }
    public function pengemasanKarton()
    {
        return $this->belongsTo(PengemasanKarton::class, 'id_pengemasan_karton');
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
