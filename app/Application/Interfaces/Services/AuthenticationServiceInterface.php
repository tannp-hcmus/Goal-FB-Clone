<?php

namespace App\Application\Interfaces\Services;

use Illuminate\Http\Request;

interface AuthenticationServiceInterface
{
    /**
     * Attempt to authenticate a user with the given credentials.
     *
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(string $email, string $password, bool $remember = false): void;

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
