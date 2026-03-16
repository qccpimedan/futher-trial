<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PemeriksaanBendaAsing extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_benda_asing';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'id_produk',
        'berat',
        'tanggal',
        'jam',
        'jenis_kontaminasi',
        'bukti',
        'kode_produksi',
        'ukuran_kontaminasi',
        'ditemukan',
        'analisa_masalah',
        'koreksi',
        'tindak_korektif',
        'diketahui',
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
    ];

    protected $casts = [
        'tanggal' => 'datetime:Y-m-d H:i:s',
        'jam' => 'datetime:H:i:s',
        // Approval fields casting
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

    // Relationships
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
        return $this->belongsTo(DataShift::class, 'shift_id', 'id');
    }

    public function produk()
    {
        return $this->belongsTo(JenisProduk::class, 'id_produk', 'id');
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

    // Scope untuk role-based access
    public function scopeForUser($query, $user)
    {
        if ($user->role !== 'superadmin') {
            return $query->where('id_plan', $user->id_plan);
        }
        return $query;
    }

    // Accessor untuk URL bukti foto
    public function getBuktiUrlAttribute()
    {
        return $this->bukti ? asset('storage/' . $this->bukti) : null;
    }

    // Method untuk check apakah user bisa akses data ini
    public function canAccess($user)
    {
        if ($user->role === 'superadmin') {
            return true;
        }
        return $this->id_plan === $user->id_plan;
    }
}
