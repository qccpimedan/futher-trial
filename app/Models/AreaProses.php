<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AreaProses extends Model
{
    use HasFactory;

    protected $table = 'area_proses';

    protected $fillable = [
        'uuid',
        'group_uuid',
        'user_id',
        'id_plan',
        'area_id',
        'shift_id',
        'tanggal',
        'jam',
        'kebersihan_ruangan',
        'kebersihan_karyawan',
        'pemeriksaan_suhu_ruang',
        'ketidaksesuaian',
        'tindakan_koreksi',
        'kondisi_barang',
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
        'kode_form',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        // Approval casts
        'approved_by_qc' => 'boolean',
        'approved_by_produksi' => 'boolean',
        'approved_by_spv' => 'boolean',
        'qc_approved_at' => 'datetime',
        'produksi_approved_at' => 'datetime',
        'spv_approved_at' => 'datetime',
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
            if (empty($model->group_uuid)) {
                // default group to itself if not provided
                $model->group_uuid = $model->uuid;
            }
        });
    }

    /**
     * Relationship to User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship to Plan model
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    /**
     * Relationship to InputArea model
     */
    public function area()
    {
        return $this->belongsTo(InputArea::class, 'area_id');
    }

    /**
     * Relationship to DataShift model
     */
    public function shift()
    {
        return $this->belongsTo(DataShift::class, 'shift_id');
    }

    /**
     * Relationship to QC Approver
     */
    public function qcApprover()
    {
        return $this->belongsTo(User::class, 'qc_approved_by');
    }

    /**
     * Relationship to Produksi Approver
     */
    public function produksiApprover()
    {
        return $this->belongsTo(User::class, 'produksi_approved_by');
    }

    /**
     * Relationship to SPV Approver
     */
    public function spvApprover()
    {
        return $this->belongsTo(User::class, 'spv_approved_by');
    }

    /**
     * Scope for filtering by plan based on user role
     */
    public function scopeForUser($query, $user)
    {
        if ($user->role === 'superadmin') {
            return $query;
        }
        
        return $query->where('id_plan', $user->id_plan);
    }

    /**
     * Scope for filtering by plan
     */
    public function scopeForPlan($query, $planId)
    {
        return $query->where('id_plan', $planId);
    }

    /**
     * Get status badge class for display
     */
    public function getStatusBadgeClass($field)
    {
        $value = $this->$field;
        
        // For enum fields (kebersihan_ruangan, kebersihan_karyawan, pemeriksaan_suhu_ruang)
        if (in_array($field, ['kebersihan_ruangan', 'kebersihan_karyawan', 'pemeriksaan_suhu_ruang'])) {
            return $value === 'OK' ? 'badge-success' : 'badge-danger';
        }
        
        // For text fields (ketidaksesuaian, tindakan_koreksi)
        if (in_array($field, ['ketidaksesuaian', 'tindakan_koreksi'])) {
            return !empty($value) ? 'badge-info' : 'badge-secondary';
        }
        
        return 'badge-secondary';
    }

    /**
     * Get status icon for display
     */
    public function getStatusIcon($field)
    {
        $value = $this->$field;
        
        // For enum fields
        if (in_array($field, ['kebersihan_ruangan', 'kebersihan_karyawan', 'pemeriksaan_suhu_ruang'])) {
            return $value === 'OK' ? 'fas fa-check' : 'fas fa-times';
        }
        
        // For text fields
        if (in_array($field, ['ketidaksesuaian', 'tindakan_koreksi'])) {
            return !empty($value) ? 'fas fa-file-text' : 'fas fa-minus';
        }
        
        return 'fas fa-question';
    }

    // Get route key name for UUID routing
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
