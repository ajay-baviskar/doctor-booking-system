<?php

namespace App\Http\Controllers\API\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\CreateAppointmentRequest;
use App\Http\Requests\Patient\UpdateAppointmentStatusRequest;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class AppointmentController extends Controller
{

    public function create(CreateAppointmentRequest $request)
    {
        try {
            if (!JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Unauthorized: Invalid or missing token',
                    'data' => null
                ], Response::HTTP_UNAUTHORIZED);
            }

            $data = $request->validated();
            $data['patient_id'] = auth()->id();

            if (Appointment::where('doctor_id', $data['doctor_id'])
                    ->where('date', $data['date'])
                    ->where('time', $data['time'])->exists()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Appointment slot is already booked',
                    'data' => null
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $appointment = Appointment::create($data);

            return response()->json([
                'status' => true,
                'msg' => 'Appointment created successfully',
                'data' => $appointment
            ], Response::HTTP_CREATED);

        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Token is invalid or expired',
                'data' => null
            ], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong. Please try again later.',
                'data' => null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function updateStatus(UpdateAppointmentStatusRequest $request, $id)
    {
        try {
            if (!JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Unauthorized: Invalid or missing token',
                    'data' => null
                ], Response::HTTP_UNAUTHORIZED);
            }

            $appointment = Appointment::where('id', $id)->where('patient_id', auth()->id())->first();

            if (!$appointment) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Appointment not found or you are not authorized to access it.',
                    'data' => null
                ], Response::HTTP_NOT_FOUND);
            }

            $appointment->update($request->validated());

            return response()->json([
                'status' => true,
                'msg' => 'Appointment status updated successfully',
                'data' => $appointment
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Validation error',
                'data' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Token is invalid or expired',
                'data' => null
            ], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong. Please try again later.',
                'data' => null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function index(Request $request)
    {
        try {
            if (!JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Unauthorized: Invalid or missing token',
                    'data' => null
                ], Response::HTTP_UNAUTHORIZED);
            }

            $appointments = Appointment::where('patient_id', auth()->id())
                ->when($request->query('date'), function ($query, $date) {
                    return $query->whereDate('date', $date);
                })
                ->get();

            return response()->json([
                'status' => true,
                'msg' => 'Appointments retrieved successfully',
                'data' => $appointments
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Token is invalid or expired',
                'data' => null
            ], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong. Please try again later.',
                'data' => null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
