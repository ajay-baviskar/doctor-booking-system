<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'Patient'; // Ensure only patients can update their appointments
    }

    public function rules()
    {
        return [
            'status' => 'required|in:Cancelled,Postponed',
        ];
    }
}
