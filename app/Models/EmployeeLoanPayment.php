<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLoanPayment extends BaseModel
{
    protected $guarded = ['id'];

    protected $casts = [
        'due_date' => 'date',
        'paid_on' => 'date',
        'amount_due' => 'float',
        'amount_paid' => 'float',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';

    protected $appends = ['outstanding_amount'];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(EmployeeLoan::class);
    }

    public function salarySlip(): BelongsTo
    {
        return $this->belongsTo(\Modules\Payroll\Entities\SalarySlip::class, 'salary_slip_id');
    }

    public function getOutstandingAmountAttribute(): float
    {
        $outstanding = ($this->amount_due ?? 0) - ($this->amount_paid ?? 0);

        return $outstanding > 0 ? (float) $outstanding : 0.0;
    }

    public function markPaid(?CarbonInterface $paidOn = null, ?string $status = null): void
    {
        $this->status = $status ?? self::STATUS_PAID;

        if ($paidOn) {
            $this->paid_on = $paidOn;
        } elseif (! $this->paid_on) {
            $this->paid_on = now();
        }
    }
}
