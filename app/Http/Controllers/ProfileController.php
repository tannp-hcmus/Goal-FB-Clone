<?php

namespace App\Http\Controllers;

use App\Application\DTOs\UpdateUserProfileDTO;
use App\Domain\Interfaces\Services\UserProfileServiceInterface;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __construct(
        private UserProfileServiceInterface $userProfileService,
    ) {}

    /**
     * Display the user's profile page.
     */
    public function show(): Response
    {
        $user = Auth::user();

        return Inertia::render('Profile/ProfilePage', [
            'user' => [
                'id' => $user->id,
                'name' => trim($user->firstname . ' ' . $user->lastname),
                'email' => $user->email,
                'avatar' => $user->avatar,
                'description' => $user->description,
            ]
        ]);
    }

    /**
     * Update the user's profile.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $dto = new UpdateUserProfileDTO(
            name: $validated['name'],
            email: $validated['email'],
            description: $validated['description'] ?? null,
        );

        $avatarFile = isset($validated['avatar']) ? $validated['avatar'] : null;

        $result = $this->userProfileService->updateProfile(
            Auth::id(),
            $dto,
            $avatarFile
        );

        if ($result['email_verification_needed']) {
            return redirect()->route('verification.notice')
                ->with('success', 'Profile updated! A verification email has been sent to your new email address.');
        }

        return back()->with('success', 'Profile updated successfully!');
    }
}
