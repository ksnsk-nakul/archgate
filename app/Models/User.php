<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot('organization_id')
            ->withTimestamps();
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'role_user')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function cachedRoles(): Collection
    {
        $key = "user:roles:{$this->id}";

        try {
            $cached = Cache::get($key);

            if ($cached instanceof Collection) {
                return $cached;
            }

            // Stale or corrupt cache entry — forget and re-fetch
            Cache::forget($key);
        } catch (\Throwable) {
            Cache::forget($key);
        }

        $roles = $this->roles()->with('permissions')->get();
        Cache::put($key, $roles, now()->addMinutes(15));

        return $roles;
    }

    public function clearRolesCache(): void
    {
        Cache::forget("user:roles:{$this->id}");
    }

    public function hasRole(string $slug, ?int $organizationId = null): bool
    {
        return $this->cachedRoles()
            ->when($organizationId, fn ($c) => $c->filter(fn ($r) => $r->pivot->organization_id == $organizationId))
            ->contains('slug', $slug);
    }

    public function hasPermission(string $slug, ?int $organizationId = null): bool
    {
        $roles = $organizationId
            ? $this->cachedRoles()->filter(fn ($r) => $r->pivot->organization_id == $organizationId)
            : $this->cachedRoles();

        return $roles->some(fn ($role) => $role->permissions->contains('slug', $slug));
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }
}
