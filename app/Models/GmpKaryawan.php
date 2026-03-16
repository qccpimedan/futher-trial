<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GmpKaryawan extends Model
{
    use HasFactory;

    protected $table = 'gmp_karyawan';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'id_area',
        'shift_id',
        'tanggal',
        'jam',
        'nama_karyawan',
        'temuan_ketidaksesuaian',
        'keterangan',
        'tindakan_koreksi',
        'verifikasi',
        'koreksi_lanjutan',
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
        'tanggal' => 'datetime:Y-m-d H:i:s',
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

    /**
     * Relationship to InputArea model
     */
    public function area()
    {
        return $this->belongsTo(InputArea::class, 'id_area');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id', 'id');
    }

    // Approval user relationships
    public function approvedByQcUser()
    {
        return $this->belongsTo(User::class, 'approved_by_qc_user_id');
    }

    public function approvedByProduksiUser()
    {
        return $this->belongsTo(User::class, 'approved_by_produksi_user_id');
    }

    public function approvedBySpvUser()
    {
        return $this->belongsTo(User::class, 'approved_by_spv_user_id');
    }
    // Scope untuk role-based access
    public function scopeForUser($query, $user)
    {
        if ($user->role !== 'superadmin') {
            return $query->where('id_plan', $user->id_plan);
        }
        return $query;
    }

    // Method untuk check apakah user bisa akses data ini
    public function canAccess($user)
    {
        if ($user->role === 'superadmin') {
            return true;
        }
        return $this->id_plan === $user->id_plan;
    }

    // Accessor untuk label temuan ketidaksesuaian
    public function getTemuanKetidaksesuaianLabelAttribute()
    {
        $labels = [
            'perlengkapan' => '1. Perlengkapan',
            'kuku' => '2. Kuku',
            'perhiasan' => '3. Perhiasan',
            'luka' => '4. Luka'
        ];

        return $labels[$this->temuan_ketidaksesuaian] ?? $this->temuan_ketidaksesuaian;
    }

    // Static method untuk mendapatkan options temuan
    public static function getTemuanOptions()
    {
        return [
            'perlengkapan' => '1. Perlengkapan',
            'kuku' => '2. Kuku',
            'perhiasan' => '3. Perhiasan',
            'luka' => '4. Luka'
        ];
    }
}
