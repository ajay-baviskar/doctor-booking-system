<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'Patient'; // Ensure only patients can book
    }

    public function rules()
    {
        return [
            'doctor_id' => 'required',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
        ];
    }

    public function messages()
    {
        return [
            'doctor_id.required' => 'The doctor ID is required.',
            'doctor_id.exists' => 'The selected doctor does not exist.',
            'date.required' => 'The appointment date is required.',
            'date.date' => 'The appointment date must be a valid date.',
            'date.after_or_equal' => 'The appointment date must be today or a future date.',
            'time.required' => 'The appointment time is required.',
            'time.date_format' => 'The appointment time must be in the format HH:mm (e.g., 14:30).',
        ];
    }
}
