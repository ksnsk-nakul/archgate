<?php

use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

function seededSuperAdmin(object $test): User
{
    $test->seed(RbacSeeder::class);

    return User::query()->where('email', 'superadmin@example.com')->firstOrFail();
}

test('superadmin can view app and third-party settings pages', function () {
    $superAdmin = seededSuperAdmin($this);

    $this->actingAs($superAdmin)
        ->get(route('admin.settings.app'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Settings/App')
            ->has('settings'));

    $this->actingAs($superAdmin)
        ->get(route('admin.settings.third-party'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Settings/ThirdParty')
            ->has('settings'));
});

test('app settings can update name logo and favicon', function () {
    Storage::fake('public');
    $superAdmin = seededSuperAdmin($this);

    $this->actingAs($superAdmin)
        ->put(route('admin.settings.app.update'), [
            'app_name' => 'Archgate Pro',
            'logo' => UploadedFile::fake()->image('logo.png', 320, 120),
            'favicon' => UploadedFile::fake()->image('favicon.png', 64, 64),
        ])
        ->assertRedirect(route('admin.settings.app'));

    $this->assertDatabaseHas(Setting::class, [
        'group' => 'app',
        'key' => 'name',
        'value' => 'Archgate Pro',
        'is_encrypted' => false,
    ]);

    expect(Setting::query()->where('group', 'app')->where('key', 'logo_path')->first()?->value)
        ->toStartWith('settings/');
    expect(Setting::query()->where('group', 'app')->where('key', 'favicon_path')->first()?->value)
        ->toStartWith('settings/');
});

test('third-party settings store secrets encrypted and preserve blank secrets', function () {
    $superAdmin = seededSuperAdmin($this);

    $this->actingAs($superAdmin)
        ->put(route('admin.settings.third-party.update'), [
            'mail_host' => 'smtp.example.com',
            'mail_port' => 2525,
            'mail_password' => 'secret-password',
            'twilio_sid' => 'AC123',
            'twilio_token' => 'twilio-secret',
            'stripe_key' => 'pk_test_123',
            'stripe_secret' => 'sk_test_123',
            'razorpay_key' => 'rzp_test_123',
            'razorpay_secret' => 'rzp_secret_123',
        ])
        ->assertRedirect(route('admin.settings.third-party'));

    $mailPassword = Setting::query()->where('group', 'mail')->where('key', 'password')->firstOrFail();

    expect($mailPassword->is_encrypted)->toBeTrue()
        ->and($mailPassword->value)->not->toBe('secret-password')
        ->and(Crypt::decryptString($mailPassword->value))->toBe('secret-password');

    $this->actingAs($superAdmin)
        ->put(route('admin.settings.third-party.update'), [
            'mail_host' => 'smtp2.example.com',
            'mail_password' => '',
        ])
        ->assertRedirect(route('admin.settings.third-party'));

    expect(Crypt::decryptString($mailPassword->refresh()->value))->toBe('secret-password');
    $this->assertDatabaseHas(Setting::class, [
        'group' => 'mail',
        'key' => 'host',
        'value' => 'smtp2.example.com',
    ]);
});

test('rbac seeder creates system roles permissions and superadmin assignment', function () {
    $superAdmin = seededSuperAdmin($this);

    foreach (['superadmin', 'admin', 'author', 'tutor', 'user'] as $slug) {
        $this->assertDatabaseHas(Role::class, [
            'slug' => $slug,
            'is_system' => true,
        ]);
    }

    foreach (['settings.app.update', 'settings.third-party.update', 'roles.update-permissions'] as $slug) {
        $this->assertDatabaseHas(Permission::class, ['slug' => $slug]);
    }

    expect($superAdmin->hasRole('superadmin'))->toBeTrue()
        ->and($superAdmin->can('viewAny', Role::class))->toBeTrue();
});

test('admin can create subadmin roles and update role permissions only from policy page', function () {
    $this->seed(RbacSeeder::class);

    $admin = User::factory()->create();
    $organization = Organization::query()->where('subdomain', 'archgate')->firstOrFail();
    $adminRole = Role::query()->where('slug', 'admin')->firstOrFail();
    $admin->roles()->attach($adminRole->id, ['organization_id' => $organization->id]);

    $response = $this->actingAs($admin)
        ->post(route('admin.roles.subadmin.store'), [
            'name' => 'Operations',
            'description' => 'Operations managers',
        ]);

    $role = Role::query()->where('name', 'Operations')->firstOrFail();

    $response->assertRedirect(route('admin.roles.permissions.edit', $role));
    expect($role->is_system)->toBeFalse()
        ->and($role->created_by)->toBe($admin->id);

    $permission = Permission::query()->where('slug', 'tasks.manage')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.roles.permissions.edit', $role))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Roles/Permissions')
            ->where('role.data.id', $role->id)
            ->has('permissionGroups'));

    $this->actingAs($admin)
        ->put(route('admin.roles.permissions.update', $role), [
            'permissions' => [$permission->id],
        ])
        ->assertRedirect(route('admin.roles.permissions.edit', $role));

    expect($role->refresh()->permissions()->pluck('slug')->all())->toBe(['tasks.manage']);
});

test('non admin users cannot manage settings or roles', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.settings.app'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.roles.index'))->assertForbidden();
});
