<?php

namespace App\Http\Controllers;

use App\Services\GoogleAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;

class GoogleAuthController extends Controller
{
    public function __construct(
        protected GoogleAuthService $googleAuthService
    ) {}

    public function redirectToGoogle()
    {
        return $this->googleAuthService->redirectToGoogle();
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->away(
                config('app.frontend_url').'/login?error='.$request->get('error')
            );
        }

        try {
            $result = $this->googleAuthService->handleGoogleCallback();

            $user = $result['user'];

            Auth::login($user);

            return redirect()->away(
                config('app.frontend_url').'/workspaces'
            );

        } catch (\Throwable $e) {
            Log::error('Google OAuth Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->away(
                config('app.frontend_url').'/login?error=oauth_failed'
            );
        }
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json(
            new UserResource($user)
        );
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated',
            ], 401);
        }

        $this->googleAuthService->revokeGoogleAccess($user);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout success',
        ]);
    }
}
