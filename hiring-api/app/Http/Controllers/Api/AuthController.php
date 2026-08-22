<?php

namespace App\Http\Controllers\Api;

use App\Actions\UserChangePassword;
use App\Actions\UserCreate;
use App\Actions\UserLogin;
use App\Actions\UserUpdateProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword']]);
    }

    /**
     * Get a JWT via given credentials.
     */
    public function login(LoginRequest $request)
    {
        $user = (new UserLogin)->handle($request->email, $request->password);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact support.'],
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token, $user);
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request)
    {
        $user = (new UserCreate)->handle($request->validated());

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token, $user)->setStatusCode(201);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request)
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Refresh a token.
     */
    public function refresh()
    {
        $user = Auth::user();

        $token = JWTAuth::parseToken()->refresh();

        return $this->respondWithToken($token, $user);
    }

    /**
     * Get the authenticated User.
     */
    public function me(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = (new UserUpdateProfile)->handle(Auth::user(), $request->validated());

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => $user,
        ]);
    }

    /**
     * Change user password.
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        (new UserChangePassword)->handle($user, $request->current_password, $request->password);

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }

    /**
     * Send password reset link.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        return response()->json([
            'message' => 'If the email exists, a reset link has been sent.',
        ]);
    }

    /**
     * Reset password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        return response()->json([
            'message' => 'Password reset successfully.',
        ]);
    }

    /**
     * Get token structure.
     */
    protected function respondWithToken($token, $user = null)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Authentication successful.',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => $user ?? auth('api')->user(),
            ],
        ]);
    }
}
