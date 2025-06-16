<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $successFlash = $request->session()->get('success');
        $errorFlash = $request->session()->get('error');

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => trim($request->user()->firstname . ' ' . $request->user()->lastname),
                    'email' => $request->user()->email,
                    'avatar' => $request->user()->avatar,
                    'description' => $request->user()->description,
                ] : null,
            ],
            'flash' => [
                'success' => $successFlash,
                'error' => $errorFlash,
            ],
        ];
    }
}
