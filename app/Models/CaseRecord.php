<?php

namespace App\Models;

use Database\Factories\CaseRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseRecord extends Model
{
    /** @use HasFactory<CaseRecordFactory> */
    use HasFactory;

    protected $table = 'cases';

    /**
     * Allowed status values.
     */
    public const STATUS_DRAFT = 'Draft';
    public const STATUS_PLANNING = 'Planning';
    public const STATUS_IN_REVIEW = 'In Review';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_CANCELLED = 'Cancelled';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PLANNING,
        self::STATUS_IN_REVIEW,
        self::STATUS_APPROVED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    /**
     * Allowed priority values.
     */
    public const PRIORITY_LOW = 'Low';
    public const PRIORITY_MEDIUM = 'Medium';
    public const PRIORITY_HIGH = 'High';

    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
    ];

    /**
     * Mass assignable fields.
     */
    protected $fillable = [
        'case_number',
        'patient_reference',
        'surgeon_name',
        'implant_type',
        'status',
        'priority',
        'surgery_date',
        'created_by',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'surgery_date' => 'date:Y-m-d',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Creator relationship.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for searching cases.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('case_number', 'like', "%{$term}%")
              ->orWhere('surgeon_name', 'like', "%{$term}%");
        });
    }

    /**
     * Scope for status filtering.
     */
    public function scopeWithStatus($query, ?string $status)
    {
        if (!empty($status) && in_array($status, self::STATUSES, true)) {
            return $query->where('status', $status);
        }

        return $query;
    }

    /**
     * Scope for priority filtering.
     */
    public function scopeWithPriority($query, ?string $priority)
    {
        if (!empty($priority) && in_array($priority, self::PRIORITIES, true)) {
            return $query->where('priority', $priority);
        }

        return $query;
    }
}
