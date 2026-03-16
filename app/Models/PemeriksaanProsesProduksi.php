<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PemeriksaanProsesProduksi extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_proses_produksi';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'id_area',
        'shift_id',
        'tanggal',
        'jam',
        'ketidaksesuaian',
        'uraian_permasalahan',
        'analisa_penyebab',
        'disposisi',
        'tindakan_koreksi',
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
        'spv_approved_at'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime'
    ];

    // Relationships
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function area()
    {
        return $this->belongsTo(InputArea::class, 'id_area');
    }

    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
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

    // Scope untuk filter berdasarkan role
    public function scopeFilterByRole($query)
    {
        $user = Auth::user();
        
        if ($user->role !== 'superadmin') {
            return $query->where('id_plan', $user->id_plan);
        }
        
        return $query;
    }

    // Method untuk cek akses data
    public function canAccess($user = null)
    {
        $user = $user ?: Auth::user();
        
        if ($user->role === 'superadmin') {
            return true;
        }
        return $this->id_plan === $user->id_plan;
    }

    // Accessor untuk label ketidaksesuaian
    public function getKetidaksesuaianLabelAttribute()
    {
        $labels = [
            'bahan' => '1. Bahan',
            'produk' => '2. Produk',
            'proses' => '3. Proses'
        ];

        return $labels[$this->ketidaksesuaian] ?? $this->ketidaksesuaian;
    }

    // Accessor untuk label disposisi
    public function getDisposisiLabelAttribute()
    {
        $labels = [
            'reject_musnahkan' => '1. Di-reject/dimusnahkan',
            'rework' => '2. Di-rework',
            'rework_perlakuan' => '3. Di-rework/dengan perlakuan',
            'repack' => '4. Di-repack',
            'sortir' => '5. Di-sortir'
        ];

        return $labels[$this->disposisi] ?? $this->disposisi;
    }

    // Static method untuk mendapatkan options ketidaksesuaian
    public static function getKetidaksesuaianOptions()
    {
        return [
            'bahan' => '1. Bahan',
            'produk' => '2. Produk',
            'proses' => '3. Proses'
        ];
    }

    // Static method untuk mendapatkan options disposisi
    public static function getDisposisiOptions()
    {
        return [
            'reject_musnahkan' => '1. Di-reject/dimusnahkan',
            'rework' => '2. Di-rework',
            'rework_perlakuan' => '3. Di-rework/dengan perlakuan',
            'repack' => '4. Di-repack',
            'sortir' => '5. Di-sortir'
        ];
    }
}
