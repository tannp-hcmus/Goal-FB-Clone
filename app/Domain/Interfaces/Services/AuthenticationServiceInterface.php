<?php

namespace App\Domain\Interfaces\Services;

use App\Application\DTOs\LoginDTO;
use Illuminate\Http\Request;

interface AuthenticationServiceInterface
{
    /**
     * Attempt to authenticate a user with the given credentials.
     *
     * @param LoginDTO $dto
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(LoginDTO $dto): void;

    /**
     * Check if the authentication attempts are rate limited.
     *
     * @param string $key
     * @param int $maxAttempts
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(string $key, int $maxAttempts = 5): void;

    /**
     * Generate a throttle key for rate limiting.
     *
     * @param string $email
     * @param string $ip
     * @return string
     */
    public function generateThrottleKey(string $email, string $ip): string;

    /**
     * Log the user out.
     *
     * @param Request $request
     */
    public function logout(Request $request): void;
}
