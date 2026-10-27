<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccountProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_account_profile(): void
    {
        $this->get(route('account.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_member_can_view_their_profile_and_saved_country(): void
    {
        $member = User::factory()->create();
        $country = Country::create(['name' => 'Vietnam']);
        $member->update(['id_country' => $country->id]);

        $this->actingAs($member)
            ->get(route('account.edit'))
            ->assertOk()
            ->assertViewIs('frontend.account.profile')
            ->assertSeeText('Update user')
            ->assertSeeText('New User Signup!')
            ->assertSee('value="'.$member->email.'"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('name="address"', false)
            ->assertSee('name="id_country"', false)
            ->assertSee('name="avatar"', false)
            ->assertSee('<option value="'.$country->id.'"', false)
            ->assertSee('selected>', false)
            ->assertSeeText('Vietnam');
    }

    public function test_member_can_update_profile_details_country_and_password(): void
    {
        $member = User::factory()->create();
        $country = Country::create(['name' => 'Vietnam']);

        $response = $this->actingAs($member)
            ->from(route('account.edit'))
            ->put(route('account.update'), [
                'name' => 'Updated Member',
                'email' => $member->email,
                'phone' => '0901234567',
                'address' => 'Da Nang',
                'id_country' => $country->id,
                'password' => 'new-password',
            ]);

        $response->assertRedirect(route('account.edit'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'name' => 'Updated Member',
            'phone' => '0901234567',
            'address' => 'Da Nang',
            'id_country' => $country->id,
        ]);
        $this->assertTrue(Hash::check('new-password', $member->fresh()->password));
    }

    public function test_member_cannot_save_an_unknown_country_id(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->from(route('account.edit'))
            ->put(route('account.update'), [
                'name' => $member->name,
                'email' => $member->email,
                'id_country' => 999,
            ])
            ->assertSessionHasErrors('id_country');

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'id_country' => null,
        ]);
    }

    public function test_member_can_upload_a_new_avatar_and_replaces_the_old_avatar(): void
    {
        Storage::fake('public');

        $oldAvatarPath = 'upload/user/avatar/old.png';
        Storage::disk('public')->put($oldAvatarPath, 'old image');
        $member = User::factory()->create(['avatar' => $oldAvatarPath]);
        $avatar = UploadedFile::fake()->createWithContent(
            'avatar.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aHF8AAAAASUVORK5CYII='),
        );

        $response = $this->actingAs($member)
            ->from(route('account.edit'))
            ->put(route('account.update'), [
                'name' => $member->name,
                'email' => $member->email,
                'avatar' => $avatar,
            ]);

        $response->assertRedirect(route('account.edit'));
        $newAvatarPath = $member->fresh()->avatar;
        $this->assertNotSame($oldAvatarPath, $newAvatarPath);
        Storage::disk('public')->assertExists($newAvatarPath);
        Storage::disk('public')->assertMissing($oldAvatarPath);
    }

    public function test_authenticated_header_renders_logout_with_menu_class(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->get('/')
            ->assertOk()
            ->assertSee('class="shop-menu-logout"', false)
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('Logout');
    }
}
