<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Thermometer extends Model
{
    use HasFactory;

    protected $table = 'thermometer';

    protected $fillable = [
        'uuid',
        'kode_form',
        'id_plan',
        'user_id',
        'shift_id',
        'tanggal',
        'jam',
        'hasil_verifikasi_0',
        'hasil_verifikasi_100',
        'jenis',
        'kode_thermometer',
        'hasil_pengecekan',
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
        'spv_approved_at' => 'datetime',
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

    // Accessor untuk label hasil pengecekan
    public function getHasilPengecekanLabelAttribute()
    {
        $labels = [
            'ok' => '✓ OK',
            'tidak_ok' => '✗ Tidak OK'
        ];

        return $labels[$this->hasil_pengecekan] ?? $this->hasil_pengecekan;
    }

    // Static method untuk mendapatkan options hasil pengecekan
    public static function getHasilPengecekanOptions()
    {
        return [
            'ok' => '✓ OK',
            'tidak_ok' => '✗ Tidak OK'
        ];
    }
}
