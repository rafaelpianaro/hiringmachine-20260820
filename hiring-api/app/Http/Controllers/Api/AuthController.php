<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $user = JWTAuth::user();

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Sua conta está desativada. Entre em contato com o suporte.'],
            ]);
        }

        return $this->respondWithToken($token, $user);
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'phone' => $request->phone,
            'company' => $request->company,
            'position' => $request->position,
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token, $user)->setStatusCode(201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::parseToken());

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = JWTAuth::parseToken()->refresh();

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = JWTAuth::user();

        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Update user profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $user = JWTAuth::user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'company' => 'sometimes|string|max:255',
            'position' => 'sometimes|string|max:255',
            'avatar' => 'sometimes|string|max:255',
        ]);

        $user->update($request->only([
            'name',
            'phone',
            'company',
            'position',
            'avatar',
        ]));

        return response()->json([
            'message' => 'Perfil atualizado com sucesso.',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Change user password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = JWTAuth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['A senha atual está incorreta.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Senha alterada com sucesso.',
        ]);
    }

    /**
     * Send password reset link.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        return response()->json([
            'message' => 'Se o email existir, um link de redefinição foi enviado.',
        ]);
    }

    /**
     * Reset password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        return response()->json([
            'message' => 'Senha redefinida com sucesso.',
        ]);
    }

    /**
     * Get token structure.
     *
     * @param string $token
     * @param User|null $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $user = null)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Autenticação realizada com sucesso.',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => $user ?? JWTAuth::user(),
            ],
        ]);
    }
}
