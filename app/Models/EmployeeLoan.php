<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\EmployeeLoanPayment;


class EmployeeLoan extends BaseModel
{
    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'principal_amount' => 'float',
        'installment_amount' => 'float',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(EmployeeLoanPayment::class);
    }

    public function refreshStatus(): void
    {
        $hasOutstandingInstallments = $this->payments()
            ->where(function ($query) {
                $query->where('status', '!=', EmployeeLoanPayment::STATUS_PAID)
                    ->orWhereColumn('amount_paid', '<', 'amount_due');
            })
            ->exists();

        $nextStatus = $hasOutstandingInstallments ? self::STATUS_ACTIVE : self::STATUS_CLOSED;

        if ($this->status !== $nextStatus) {
            $this->status = $nextStatus;
            $this->save();
        }
    }
}
