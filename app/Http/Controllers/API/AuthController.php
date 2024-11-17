<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role' => 'required|in:Patient,Doctor',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => true,
                'msg' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ], Response::HTTP_CREATED);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Unauthorized. Invalid credentials.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json([
                'status' => true,
                'msg' => 'Login successful',
                'data' => $this->respondWithToken($token),
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Token generation failed',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }
}
