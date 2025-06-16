<?php

namespace Tests\Feature;

use App\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'description' => 'This is a test description',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $user->refresh();

        $this->assertSame('Test', $user->firstname);
        $this->assertSame('User', $user->lastname);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('This is a test description', $user->description);
    }

    public function test_avatar_can_be_uploaded()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this
            ->actingAs($user)
            ->post('/profile', [
                'name' => $user->firstname . ' ' . $user->lastname,
                'email' => $user->email,
                'avatar' => $file,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $user->refresh();

        $this->assertNotNull($user->avatar);
        $this->assertStringContainsString('avatars', $user->avatar);

        // Check that file was actually stored
        $avatarPath = str_replace('/storage/', '', $user->avatar);
        $this->assertTrue(Storage::disk('public')->exists($avatarPath));
    }

    public function test_email_verification_status_is_unchanged_when_email_is_unchanged()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_is_redirected_to_verification_when_email_changes()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/profile', [
                'name' => 'Test User',
                'email' => 'newemail@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/verify-email')
            ->assertSessionHas('success', 'Profile updated! A verification email has been sent to your new email address.');

        $user->refresh();

        $this->assertSame('newemail@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_name_is_required()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/profile', [
                'name' => '',
                'email' => $user->email,
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_email_is_required()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/profile', [
                'name' => 'Test User',
                'email' => '',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_email_must_be_valid()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/profile', [
                'name' => 'Test User',
                'email' => 'invalid-email',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_description_cannot_exceed_255_characters()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
                'description' => str_repeat('a', 256),
            ]);

        $response->assertSessionHasErrors('description');
    }
}
