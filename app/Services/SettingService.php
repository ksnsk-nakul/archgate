<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    /**
     * @return array<string, mixed>
     */
    public function appSettings(): array
    {
        return [
            'app_name' => $this->get('app', 'name', config('app.name')),
            'logo_path' => $this->get('app', 'logo_path'),
            'logo_url' => $this->publicUrl($this->get('app', 'logo_path')),
            'favicon_path' => $this->get('app', 'favicon_path'),
            'favicon_url' => $this->publicUrl($this->get('app', 'favicon_path')),
            'primary_color' => $this->get('app', 'primary_color', '#f7600d'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function thirdPartySettings(): array
    {
        $settings = [
            'mail_mailer' => $this->get('mail', 'mailer', config('mail.default')),
            'mail_host' => $this->get('mail', 'host', config('mail.mailers.smtp.host')),
            'mail_port' => $this->get('mail', 'port', config('mail.mailers.smtp.port')),
            'mail_username' => $this->get('mail', 'username', config('mail.mailers.smtp.username')),
            'mail_encryption' => $this->get('mail', 'encryption', config('mail.mailers.smtp.encryption')),
            'mail_from_address' => $this->get('mail', 'from_address', config('mail.from.address')),
            'mail_from_name' => $this->get('mail', 'from_name', config('mail.from.name')),
            'twilio_sid' => $this->get('twilio', 'sid'),
            'twilio_from' => $this->get('twilio', 'from'),
            'stripe_key' => $this->get('stripe', 'key'),
            'razorpay_key' => $this->get('razorpay', 'key'),
        ];

        foreach (['mail_password', 'twilio_token', 'stripe_secret', 'razorpay_secret'] as $secret) {
            [$group, $key] = explode('_', $secret, 2);
            $settings[$secret.'_configured'] = $this->exists($group, $key);
        }

        return $settings;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateAppSettings(array $attributes): void
    {
        $this->set('app', 'name', $attributes['app_name']);

        if (isset($attributes['primary_color']) && $attributes['primary_color']) {
            $this->set('app', 'primary_color', $attributes['primary_color']);
        }

        if (($attributes['logo'] ?? null) instanceof UploadedFile) {
            $this->set('app', 'logo_path', $attributes['logo']->store('settings', 'public'));
        }

        if (($attributes['favicon'] ?? null) instanceof UploadedFile) {
            $this->set('app', 'favicon_path', $attributes['favicon']->store('settings', 'public'));
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateThirdPartySettings(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            match ($key) {
                'mail_mailer' => $this->set('mail', 'mailer', $value),
                'mail_host' => $this->set('mail', 'host', $value),
                'mail_port' => $this->set('mail', 'port', (string) $value),
                'mail_username' => $this->set('mail', 'username', $value),
                'mail_password' => $this->set('mail', 'password', $value, true),
                'mail_encryption' => $this->set('mail', 'encryption', $value),
                'mail_from_address' => $this->set('mail', 'from_address', $value),
                'mail_from_name' => $this->set('mail', 'from_name', $value),
                'twilio_sid' => $this->set('twilio', 'sid', $value),
                'twilio_token' => $this->set('twilio', 'token', $value, true),
                'twilio_from' => $this->set('twilio', 'from', $value),
                'stripe_key' => $this->set('stripe', 'key', $value),
                'stripe_secret' => $this->set('stripe', 'secret', $value, true),
                'razorpay_key' => $this->set('razorpay', 'key', $value),
                'razorpay_secret' => $this->set('razorpay', 'secret', $value, true),
                default => null,
            };
        }
    }

    /**
     * @return array<string, mixed>
     */
    /** @return array<string, mixed> */
    public function landingSettings(): array
    {
        $defaultPageEnabled = json_encode([
            'home' => true, 'services' => true, 'about' => true, 'careers' => true,
            'contact' => true, 'library-preview' => true, 'footer-nav' => true,
        ]);

        return [
            'hero_title' => $this->get('landing', 'hero_title', 'Build, Learn & Grow with FluxHaven'),
            'hero_subtitle' => $this->get('landing', 'hero_subtitle', 'The all-in-one platform for teams, learners, and businesses.'),
            'cta_label' => $this->get('landing', 'cta_label', 'Get started free'),
            'nav_links' => $this->get('landing', 'nav_links', json_encode([
                ['label' => 'Home', 'href' => '/'],
                ['label' => 'Services', 'href' => '/services'],
                ['label' => 'Library', 'href' => '/library-preview'],
                ['label' => 'About', 'href' => '/about'],
                ['label' => 'Careers', 'href' => '/careers'],
                ['label' => 'Contact', 'href' => '/contact'],
            ])),
            'services' => $this->get('landing', 'services', json_encode([
                ['icon' => 'briefcase', 'title' => 'Project Management', 'body' => 'Track projects, tasks, and deadlines in one place.'],
                ['icon' => 'users', 'title' => 'CRM & Deals', 'body' => 'Manage leads, contacts, and your sales pipeline.'],
                ['icon' => 'book', 'title' => 'Library', 'body' => 'Access books, articles, and learning resources.'],
                ['icon' => 'graduation', 'title' => 'Courses', 'body' => 'Structured learning paths with tutor support.'],
            ])),
            'about_text' => $this->get('landing', 'about_text', 'FluxHaven is built for modern teams who want to learn and ship faster.'),
            'footer_text' => $this->get('landing', 'footer_text', '© '.date('Y').' FluxHaven. All rights reserved.'),
            'careers' => $this->get('landing', 'careers', json_encode([])),
            'contact_email' => $this->get('landing', 'contact_email', ''),
            'contact_phone' => $this->get('landing', 'contact_phone', ''),
            'contact_address' => $this->get('landing', 'contact_address', ''),
            'page_enabled' => json_decode($this->get('landing', 'page_enabled', $defaultPageEnabled) ?? $defaultPageEnabled, true),
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateLandingSettings(array $attributes): void
    {
        foreach (['hero_title', 'hero_subtitle', 'cta_label', 'about_text', 'footer_text', 'contact_email', 'contact_phone', 'contact_address'] as $key) {
            if (array_key_exists($key, $attributes)) {
                $this->set('landing', $key, $attributes[$key]);
            }
        }

        foreach (['nav_links', 'services', 'careers'] as $jsonKey) {
            if (array_key_exists($jsonKey, $attributes)) {
                $this->set('landing', $jsonKey, is_string($attributes[$jsonKey]) ? $attributes[$jsonKey] : json_encode($attributes[$jsonKey]));
            }
        }

        if (array_key_exists('page_enabled', $attributes) && is_array($attributes['page_enabled'])) {
            // Always force home to true
            $attributes['page_enabled']['home'] = true;
            $this->set('landing', 'page_enabled', json_encode($attributes['page_enabled']));
        }
    }

    public function get(string $group, string $key, ?string $default = null): ?string
    {
        $setting = Setting::query()
            ->where('group', $group)
            ->where('key', $key)
            ->first();

        if (! $setting) {
            return $default;
        }

        if ($setting->is_encrypted && $setting->value !== null) {
            return Crypt::decryptString($setting->value);
        }

        return $setting->value ?? $default;
    }

    private function set(string $group, string $key, ?string $value, bool $encrypted = false): void
    {
        Setting::query()->updateOrCreate(
            ['group' => $group, 'key' => $key],
            [
                'value' => $encrypted && $value !== null ? Crypt::encryptString($value) : $value,
                'is_encrypted' => $encrypted,
            ],
        );
    }

    private function exists(string $group, string $key): bool
    {
        return Setting::query()
            ->where('group', $group)
            ->where('key', $key)
            ->whereNotNull('value')
            ->exists();
    }

    private function publicUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
