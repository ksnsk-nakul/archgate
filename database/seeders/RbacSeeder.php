<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RbacSeeder extends Seeder
{
    /**
     * @var array<string, array<int, array{0: string, 1: string}>>
     */
    private array $permissions = [
        'Settings' => [
            ['settings.view', 'View settings'],
            ['settings.app.update', 'Update app settings'],
            ['settings.third-party.update', 'Update third-party settings'],
        ],
        'Roles' => [
            ['roles.view', 'View roles'],
            ['roles.create', 'Create subadmin roles'],
            ['roles.update-permissions', 'Update role permissions'],
        ],
        'Projects' => [
            ['projects.view', 'View projects'],
            ['projects.manage', 'Manage projects'],
        ],
        'Tasks' => [
            ['tasks.view', 'View tasks'],
            ['tasks.manage', 'Manage tasks'],
            ['tasks.history.view', 'View task history'],
        ],
        'CRM' => [
            ['contacts.manage', 'Manage contacts'],
            ['deals.manage', 'Manage deals'],
        ],
        'Learning' => [
            ['courses.manage', 'Manage courses'],
            ['lessons.manage', 'Manage lessons'],
            ['library.manage', 'Manage library'],
        ],
    ];

    public function run(): void
    {
        $permissions = $this->seedPermissions();
        $roles = $this->seedRoles();

        $roles['superadmin']->permissions()->sync($permissions->pluck('id')->all());
        $roles['admin']->permissions()->sync($permissions->pluck('id')->all());
        $roles['author']->permissions()->sync(
            $permissions->whereIn('slug', ['courses.manage', 'lessons.manage', 'library.manage'])->pluck('id')->all(),
        );
        $roles['tutor']->permissions()->sync(
            $permissions->whereIn('slug', ['courses.manage', 'lessons.manage', 'tasks.view'])->pluck('id')->all(),
        );
        $roles['user']->permissions()->sync(
            $permissions->whereIn('slug', ['projects.view', 'tasks.view', 'tasks.history.view'])->pluck('id')->all(),
        );

        $organization = Organization::query()->firstOrCreate(
            ['subdomain' => 'archgate'],
            ['name' => 'Archgate', 'settings' => null],
        );

        $superAdmin = User::query()->firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $superAdmin->roles()->syncWithoutDetaching([
            $roles['superadmin']->id => ['organization_id' => $organization->id],
        ]);
        $superAdmin->clearRolesCache();
    }

    /**
     * @return Collection<int, Permission>
     */
    private function seedPermissions()
    {
        $deletablePermissions = Permission::query()
            ->whereNotIn('slug', collect($this->permissions)->flatten(1)->pluck(0))
            ->delete();
        foreach ($this->permissions as $group => $permissions) {
            foreach ($permissions as [$slug, $name]) {
                Permission::query()->firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'description' => $name,
                        'group' => $group,
                    ],
                );
            }
        }

        return Permission::query()->get();
    }

    /**
     * @return array<string, Role>
     */
    private function seedRoles(): array
    {
        $roles = [
            'superadmin' => ['Super Admin', 'Full platform control.'],
            'admin' => ['Admin', 'Tenant administration and subadmin management.'],
            'author' => ['Author', 'Content authoring access.'],
            'tutor' => ['Tutor', 'Course and task support access.'],
            'user' => ['User', 'Standard account access.'],
        ];

        return collect($roles)
            ->mapWithKeys(fn (array $role, string $slug): array => [
                $slug => Role::query()->firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $role[0],
                        'description' => $role[1],
                        'is_system' => true,
                    ],
                ),
            ])
            ->all();
    }
}
