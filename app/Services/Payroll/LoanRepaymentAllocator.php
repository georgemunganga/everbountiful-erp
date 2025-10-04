<?php

namespace App\Services\Payroll;

use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Payroll\Entities\SalarySlip;

class LoanRepaymentAllocator
{
    public function getDeductionsForPeriod(User $user, Carbon $periodStart, Carbon $periodEnd): array
    {
        $payments = EmployeeLoanPayment::with('loan')
            ->whereHas('loan', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', EmployeeLoan::STATUS_ACTIVE);
            })
            ->whereNull('salary_slip_id')
            ->whereDate('due_date', '>=', $periodStart->toDateString())
            ->whereDate('due_date', '<=', $periodEnd->toDateString())
            ->orderBy('due_date')
            ->get();

        $entries = [];
        $total = 0.0;
        $processedPayments = collect();

        foreach ($payments as $payment) {
            if ($payment->outstanding_amount <= 0) {
                if ($payment->status !== EmployeeLoanPayment::STATUS_PAID) {
                    $payment->markPaid();
                    $payment->save();
                }

                continue;
            }

            $label = sprintf(
                'Loan - %s (%s)',
                $payment->loan->title,
                $payment->due_date ? $payment->due_date->format('M d, Y') : ''
            );

            $entries[] = [
                'label' => $label,
                'amount' => $payment->outstanding_amount,
            ];

            $total += $payment->outstanding_amount;
            $processedPayments->push($payment);
        }

        return [
            'entries' => $entries,
            'total' => $total,
            'payments' => $processedPayments,
        ];
    }

    public function markProcessed(Collection $payments, SalarySlip $salarySlip): void
    {
        $payments->each(function (EmployeeLoanPayment $payment) use ($salarySlip) {
            $outstanding = $payment->outstanding_amount;

            if ($outstanding <= 0) {
                return;
            }

            $payment->amount_paid = round($payment->amount_paid + $outstanding, 2);
            $payment->salary_slip_id = $salarySlip->id;
            $payment->markPaid($salarySlip->salary_to ?? now());
            $payment->save();
        });
    }
}
