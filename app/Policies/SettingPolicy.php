<?php

namespace App\Policies;

use App\Models\User;

class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('settings.view');
    }

    public function updateApp(User $user): bool
    {
        return $user->hasPermission('settings.app.update');
    }

    public function updateThirdParty(User $user): bool
    {
        return $user->hasPermission('settings.third-party.update');
    }
}
