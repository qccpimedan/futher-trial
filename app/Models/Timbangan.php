<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Timbangan extends Model
{
    use HasFactory;

    protected $table = 'timbangan';

    protected $fillable = [
        'uuid',
        'kode_form',
        'id_plan',
        'user_id',
        'shift_id',
        'tanggal',
        'jam',
        'jenis',
        'kode_timbangan',
        'hasil_pengecekan',
        'gram',
        'hasil_verifikasi_500',
        'hasil_verifikasi_1000',
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

    /**
     * Boot function untuk auto-generate UUID
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    /**
     * Route key name untuk menggunakan UUID sebagai parameter
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Relationship ke Plan
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    /**
     * Relationship ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship ke DataShift
     */
    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    /**
     * Scope untuk filter berdasarkan role user
     */
    public function scopeFilterByRole($query)
    {
        $user = Auth::user();
        
        if ($user && $user->role !== 'superadmin') {
            return $query->where('id_plan', $user->id_plan);
        }
        
        return $query;
    }

    /**
     * Cek apakah user dapat mengakses data ini
     */
    public function canAccess()
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        if ($user->role === 'superadmin') {
            return true;
        }
        
        return $this->id_plan == $user->id_plan;
    }

    /**
     * Accessor untuk label hasil pengecekan dengan simbol
     */
    public function getHasilPengecekanLabelAttribute()
    {
        $labels = [
            'ok' => '✓ OK',
            'tidak_ok' => '✗ Tidak OK'
        ];

        return $labels[$this->hasil_pengecekan] ?? $this->hasil_pengecekan;
    }

    /**
     * Accessor untuk label gram
     */
    public function getGramLabelAttribute()
    {
        $options = self::getGramOptions();
        return $options[$this->gram] ?? $this->gram;
    }

    /**
     * Relationship untuk QC approver
     */
    public function qcApprover()
    {
        return $this->belongsTo(User::class, 'qc_approved_by');
    }

    /**
     * Relationship untuk Produksi approver
     */
    public function produksiApprover()
    {
        return $this->belongsTo(User::class, 'produksi_approved_by');
    }

    /**
     * Relationship untuk SPV approver
     */
    public function spvApprover()
    {
        return $this->belongsTo(User::class, 'spv_approved_by');
    }

    /**
     * Static method untuk mendapatkan options hasil pengecekan
     */
    public static function getHasilPengecekanOptions()
    {
        return [
            'ok' => '✓ OK',
            'tidak_ok' => '✗ Tidak OK'
        ];
    }

    /**
     * Static method untuk mendapatkan options gram
     */
    public static function getGramOptions()
    {
        return [
            '500' => '500 Gram',
            '1000' => '1000 Gram'
        ];
    }
}
