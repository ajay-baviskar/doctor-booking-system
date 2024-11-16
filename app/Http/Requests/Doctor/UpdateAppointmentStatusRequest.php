<?php
namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'Doctor'; // Ensure only doctors can update statuses
    }

    public function rules()
    {
        return [
            'status' => 'required|in:Approved,Rejected',
        ];
    }
}
