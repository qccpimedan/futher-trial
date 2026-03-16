<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KontrolSanitasi extends Model
{
    use HasFactory;

    protected $table = 'kontrol_sanitasi';

    protected $fillable = [
        'uuid',
        'id_plan',
        'user_id',
        'shift_id',
        'tanggal',
        'jam',
        'suhu_air',
        'kadar_klorin_food_basin',
        'kadar_klorin_hand_basin',
        'hasil_verifikasi',
        'kode_form',
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
        'tanggal' => 'datetime',
        'suhu_air' => 'string',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        date_default_timezone_set('Asia/Jakarta');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
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

    // Scopes for role-based access
    public function scopeForUser($query, $user)
    {
        if ($user->role === 'superadmin') {
            return $query;
        }
        
        return $query->where('id_plan', $user->id_plan);
    }

    public function scopeForPlan($query, $planId)
    {
        return $query->where('id_plan', $planId);
    }

    // Helper methods for display
    public function getFormattedSuhuAirAttribute()
    {
        return $this->suhu_air . '°C';
    }

    public function getStatusBadgeClass()
    {
        $hasil = strtolower($this->hasil_verifikasi);
        
        if (str_contains($hasil, 'sesuai') || str_contains($hasil, 'ok') || str_contains($hasil, 'baik')) {
            return 'badge-success';
        } elseif (str_contains($hasil, 'tidak') || str_contains($hasil, 'buruk') || str_contains($hasil, 'gagal')) {
            return 'badge-danger';
        } else {
            return 'badge-warning';
        }
    }

    public function getStatusIcon()
    {
        $hasil = strtolower($this->hasil_verifikasi);
        
        if (str_contains($hasil, 'sesuai') || str_contains($hasil, 'ok') || str_contains($hasil, 'baik')) {
            return 'fas fa-check';
        } elseif (str_contains($hasil, 'tidak') || str_contains($hasil, 'buruk') || str_contains($hasil, 'gagal')) {
            return 'fas fa-times';
        } else {
            return 'fas fa-exclamation';
        }
    }

    // Get route key name for UUID routing
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
