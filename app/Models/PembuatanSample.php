<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class PembuatanSample extends Model
{
    use HasFactory;

    protected $table = 'pembuatan_sample';

    protected $fillable = [
        'uuid',
        'id_produk',
        'id_plan',
        'created_by',
        'id_shift',
        'kode_produksi',
        'tanggal',
         'jam',
        'tanggal_expired',
        'jumlah',
        'berat',
        'berat_sampling',
        'jenis_sample',
        'kode_form', // NOTE: kode_form hanya dapat diisi melalui modal PDF export, tidak melalui form create/edit
        'approved_by_qc',
        'approved_by_produksi',
        'approved_by_spv',
        'qc_approved_by',
        'produksi_approved_by',
        'spv_approved_by',
        'qc_approved_at',
        'produksi_approved_at',
        'spv_approved_at',
    ];
    protected $casts = [
        // Pastikan waktu tidak terpotong (00:00:00). Simpan & serialize sebagai datetime lengkap.
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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
