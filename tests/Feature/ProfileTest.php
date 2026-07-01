<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test profile edit page is protected by auth.
     */
    public function test_profile_page_requires_authentication(): void
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can view their profile.
     */
    public function test_user_can_view_profile_page(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'role' => 'resident',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('Resident');
    }

    /**
     * Test user can update profile details.
     */
    public function test_user_can_update_profile_information(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567',
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '7654321',
            'gender' => 'female',
            'address' => '123 Main St',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success', 'Profile updated successfully.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '7654321',
            'gender' => 'female',
            'address' => '123 Main St',
        ]);
    }

    /**
     * Test profile email validation requires uniqueness.
     */
    public function test_user_cannot_update_email_to_another_users_email(): void
    {
        $user1 = User::factory()->create(['email' => 'john@example.com']);
        $user2 = User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->actingAs($user1)->put(route('profile.update'), [
            'name' => 'John New Name',
            'email' => 'jane@example.com', // Conflict
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals('john@example.com', $user1->fresh()->email);
    }

    /**
     * Test user can upload avatar.
     */
    public function test_user_can_upload_avatar(): void
    {
        $user = User::factory()->create();
        $avatar = UploadedFile::fake()->image('avatar.png');

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $avatar,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();

        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
    }

    /**
     * Test user can remove avatar.
     */
    public function test_user_can_remove_avatar(): void
    {
        $user = User::factory()->create([
            'avatar' => 'avatars/dummy.png',
        ]);
        
        Storage::disk('public')->put('avatars/dummy.png', 'fake content');

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'remove_avatar' => '1',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $user->refresh();

        $this->assertNull($user->avatar);
        Storage::disk('public')->assertMissing('avatars/dummy.png');
    }

    /**
     * Test password change functionality.
     */
    public function test_user_can_change_password_securely(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'oldpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success', 'Password changed successfully.');

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /**
     * Test incorrect current password returns validation error.
     */
    public function test_password_change_fails_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('correctpassword', $user->fresh()->password));
    }
}
