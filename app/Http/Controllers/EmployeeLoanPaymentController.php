<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\EmployeeLoanPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeLoanPaymentController extends AccountBaseController
{
    public function store(Request $request)
    {
        $this->addPermission = user()->permission('add_payroll');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $data = $request->validate([
            'employee_loan_payment_id' => 'required|exists:employee_loan_payments,id',
            'amount' => 'required|numeric|min:0.01',
            'paid_on' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $loanPayment = EmployeeLoanPayment::with('loan')->findOrFail($data['employee_loan_payment_id']);

        $amount = round($data['amount'], 2);
        $outstanding = $loanPayment->outstanding_amount;

        if ($outstanding <= 0) {
            return Reply::error(__('payroll::messages.installmentAlreadySettled'));
        }

        $appliedAmount = min($amount, $outstanding);
        $responseMessage = __('messages.recordSaved');

        if ($amount > $outstanding) {
            $responseMessage = __('payroll::messages.paymentAmountCapped');
        }

        $loanPayment->amount_paid = round(($loanPayment->amount_paid ?? 0) + $appliedAmount, 2);
        $loanPayment->salary_slip_id = null;

        if (!empty($data['notes'])) {
            $loanPayment->notes = $data['notes'];
        }

        if ($loanPayment->amount_paid >= $loanPayment->amount_due) {
            $paidOn = $data['paid_on']
                ? Carbon::parse($data['paid_on'])->setTimezone($this->company->timezone)
                : now()->setTimezone($this->company->timezone);

            $loanPayment->markPaid($paidOn);
        } else {
            $loanPayment->status = EmployeeLoanPayment::STATUS_PARTIAL;

            if (!empty($data['paid_on'])) {
                $loanPayment->paid_on = Carbon::parse($data['paid_on'])->setTimezone($this->company->timezone);
            }
        }

        $loanPayment->save();

        $loanPayment->loan?->refreshStatus();

        return Reply::success($responseMessage);
    }
}
