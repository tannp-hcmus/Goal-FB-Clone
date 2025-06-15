<?php

namespace App\Http\Controllers\Auth;

use App\Application\DTOs\RegisterUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Domain\Interfaces\Services\UserServiceInterface;

class RegisteredUserController extends Controller
{
    public function __construct(
        private UserServiceInterface $userService
    ) {}

    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $dto = RegisterUserDTO::fromArray($request->validated());
        $user = $this->userService->create($dto);

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
