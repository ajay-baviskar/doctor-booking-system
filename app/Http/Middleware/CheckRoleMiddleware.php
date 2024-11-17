<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{

    public function handle($request, Closure $next, $role)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if ($user->role !== $role) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Access denied: Insufficient permissions',
                    'data' => null,
                ], Response::HTTP_FORBIDDEN);
            }
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Token has expired',
                'data' => null,
            ], Response::HTTP_UNAUTHORIZED);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Token is invalid',
                'data' => null,
            ], Response::HTTP_UNAUTHORIZED);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Token is missing or not parsed correctly',
                'data' => null,
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong. Please try again later.',
                'data' => null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $next($request);
    }
}
