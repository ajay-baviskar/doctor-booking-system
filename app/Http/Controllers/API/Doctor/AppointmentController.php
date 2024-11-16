<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateAppointmentStatusRequest;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AppointmentController extends Controller
{

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

            $appointments = Appointment::where('doctor_id', auth()->id())
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

            $appointment = Appointment::where('id', $id)->where('doctor_id', auth()->id())->first();

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
}
