<?php

namespace App\Http\Requests\Auth;

use App\Application\DTOs\LoginDTO;
use App\Domain\Interfaces\Services\AuthenticationServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * @var AuthenticationServiceInterface
     */
    private $authenticationService;

    /**
     * Create a new request instance.
     */
    public function __construct(AuthenticationServiceInterface $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $validated = $this->validated();
        $dto = LoginDTO::fromArray([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'remember' => $validated['remember'] ?? false,
        ]);

        $this->authenticationService->authenticate($dto);
    }
}
