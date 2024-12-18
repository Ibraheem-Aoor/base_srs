<?php

namespace App\Http\Requests\Admin;

use App\Rules\Admin\CheckValidInstallmentsAmount;
use Illuminate\Foundation\Http\FormRequest;

class ApplicationInstallmentRequest extends BaseAdminRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'installments' => ['required','array' , new CheckValidInstallmentsAmount($this->student)],
            'installments.*.title' => 'required|string',
            'installments.*.amount' => 'required|numeric',
            'installments.*.payment_date' => 'required|date',
        ];
    }
}
