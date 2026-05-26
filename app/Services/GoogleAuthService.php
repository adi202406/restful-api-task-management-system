<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function redirectToGoogle(): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(): array
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $googleData = [
                'id' => $googleUser->getId(),
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'token' => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken,
                'avatar' => $googleUser->getAvatar(),
            ];

            $user = $this->userRepository->findOrCreateFromGoogle($googleData);

            return [
                'user' => $user,
            ];

        } catch (Exception $e) {
            throw new Exception('Google authentication failed: '.$e->getMessage());
        }
    }

    public function revokeGoogleAccess(User $user): bool
    {
        if ($user->google_token) {
            $user->update([
                'google_token' => null,
                'google_refresh_token' => null,
            ]);

            return true;
        }

        return false;
    }
}
