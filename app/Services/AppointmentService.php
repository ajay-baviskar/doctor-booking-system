<?php

use App\Models\Appointment;

class AppointmentService
{
    public static function create(array $data)
    {
        return Appointment::create($data);
    }

    public static function updateStatus($id, array $data)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => $data['status']]);
        return $appointment;
    }

    public static function listForPatient($patientId, $date = null)
    {
        return Appointment::where('patient_id', $patientId)
            ->when($date, fn ($query) => $query->whereDate('date', $date))
            ->get();
    }

    public static function listForDoctor($doctorId, $date = null)
    {
        return Appointment::where('doctor_id', $doctorId)
            ->when($date, fn ($query) => $query->whereDate('date', $date))
            ->get();
    }
}
