<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;


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
}
