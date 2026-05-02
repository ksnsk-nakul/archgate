<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { update } from '@/routes/admin/settings/third-party';

type ThirdPartySettings = {
    mail_mailer?: string | null;
    mail_host?: string | null;
    mail_port?: string | null;
    mail_username?: string | null;
    mail_encryption?: string | null;
    mail_from_address?: string | null;
    mail_from_name?: string | null;
    mail_password_configured?: boolean;
    twilio_sid?: string | null;
    twilio_token_configured?: boolean;
    twilio_from?: string | null;
    stripe_key?: string | null;
    stripe_secret_configured?: boolean;
    razorpay_key?: string | null;
    razorpay_secret_configured?: boolean;
};

defineProps<{
    settings: ThirdPartySettings;
}>();
</script>

<template>
    <Head title="Third-party settings" />

    <div class="flex flex-col min-h-full bg-app-bg text-app" style="font-family: Inter, sans-serif;">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-app">
            <div>
                <p class="text-xs text-app-muted font-semibold uppercase tracking-widest mb-0.5">Admin</p>
                <h1 class="text-xl font-bold text-app" style="font-family: Manrope, sans-serif;">Third-party Settings</h1>
            </div>
        </div>

        <Form v-bind="update.form()" class="px-6 py-6 flex flex-col gap-6 max-w-3xl" v-slot="{ errors, processing }">

            <!-- Mail -->
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                        <svg class="size-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Mail</h2>
                        <p class="text-xs text-app-muted">SMTP / transactional email configuration</p>
                    </div>
                </div>
                <div class="px-6 py-5 grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-1.5">
                        <label for="mail_mailer" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Mailer</label>
                        <input id="mail_mailer" name="mail_mailer" :value="settings.mail_mailer ?? ''" placeholder="smtp" class="bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors" />
                        <InputError :message="errors.mail_mailer" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="mail_host" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Host</label>
                        <input id="mail_host" name="mail_host" :value="settings.mail_host ?? ''" placeholder="smtp.example.com" class="bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors" />
                        <InputError :message="errors.mail_host" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="mail_port" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Port</label>
                        <input id="mail_port" name="mail_port" type="number" :value="settings.mail_port ?? ''" placeholder="587" class="bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors" />
                        <InputError :message="errors.mail_port" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="mail_encryption" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Encryption</label>
                        <input id="mail_encryption" name="mail_encryption" :value="settings.mail_encryption ?? ''" placeholder="tls" class="bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors" />
                        <InputError :message="errors.mail_encryption" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="mail_username" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Username</label>
                        <input id="mail_username" name="mail_username" :value="settings.mail_username ?? ''" class="bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors" />
                        <InputError :message="errors.mail_username" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="mail_password" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Password</label>
                        <div class="relative">
                            <input
                                id="mail_password"
                                name="mail_password"
                                type="password"
                                :placeholder="settings.mail_password_configured ? '● Configured — leave blank to keep' : 'Enter password'"
                                class="w-full bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors"
                            />
                            <span v-if="settings.mail_password_configured" class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-400 text-xs">✓</span>
                        </div>
                        <InputError :message="errors.mail_password" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="mail_from_address" class="text-xs font-semibold text-app-muted uppercase tracking-wider">From address</label>
                        <input id="mail_from_address" name="mail_from_address" :value="settings.mail_from_address ?? ''" placeholder="noreply@example.com" class="bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors" />
                        <InputError :message="errors.mail_from_address" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="mail_from_name" class="text-xs font-semibold text-app-muted uppercase tracking-wider">From name</label>
                        <input id="mail_from_name" name="mail_from_name" :value="settings.mail_from_name ?? ''" class="bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors" />
                        <InputError :message="errors.mail_from_name" />
                    </div>
                </div>
            </div>

            <!-- SMS / Twilio -->
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                        <svg class="size-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">SMS — Twilio</h2>
                        <p class="text-xs text-app-muted">Twilio account credentials for SMS sending</p>
                    </div>
                </div>
                <div class="px-6 py-5 grid gap-4 md:grid-cols-3">
                    <div class="flex flex-col gap-1.5">
                        <label for="twilio_sid" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Account SID</label>
                        <input id="twilio_sid" name="twilio_sid" :value="settings.twilio_sid ?? ''" placeholder="ACxxxxxxxx" class="bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors" />
                        <InputError :message="errors.twilio_sid" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="twilio_token" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Auth token</label>
                        <div class="relative">
                            <input
                                id="twilio_token"
                                name="twilio_token"
                                type="password"
                                :placeholder="settings.twilio_token_configured ? '● Configured — leave blank to keep' : 'Enter token'"
                                class="w-full bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors"
                            />
                            <span v-if="settings.twilio_token_configured" class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-400 text-xs">✓</span>
                        </div>
                        <InputError :message="errors.twilio_token" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="twilio_from" class="text-xs font-semibold text-app-muted uppercase tracking-wider">From number</label>
                        <input id="twilio_from" name="twilio_from" :value="settings.twilio_from ?? ''" placeholder="+15551234567" class="bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors" />
                        <InputError :message="errors.twilio_from" />
                    </div>
                </div>
            </div>

            <!-- Payments -->
            <div class="rounded-xl border border-app bg-app-surface overflow-hidden">
                <div class="px-6 py-4 border-b border-app flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                        <svg class="size-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-app" style="font-family: Manrope, sans-serif;">Payments</h2>
                        <p class="text-xs text-app-muted">Stripe and Razorpay payment gateway credentials</p>
                    </div>
                </div>
                <div class="px-6 py-5 grid gap-6">
                    <!-- Stripe -->
                    <div>
                        <p class="text-xs font-bold text-app-muted uppercase tracking-widest mb-3">Stripe</p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="flex flex-col gap-1.5">
                                <label for="stripe_key" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Publishable key</label>
                                <input id="stripe_key" name="stripe_key" :value="settings.stripe_key ?? ''" placeholder="pk_live_..." class="bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors" />
                                <InputError :message="errors.stripe_key" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label for="stripe_secret" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Secret key</label>
                                <div class="relative">
                                    <input
                                        id="stripe_secret"
                                        name="stripe_secret"
                                        type="password"
                                        :placeholder="settings.stripe_secret_configured ? '● Configured — leave blank to keep' : 'sk_live_...'"
                                        class="w-full bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors"
                                    />
                                    <span v-if="settings.stripe_secret_configured" class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-400 text-xs">✓</span>
                                </div>
                                <InputError :message="errors.stripe_secret" />
                            </div>
                        </div>
                    </div>
                    <!-- Razorpay -->
                    <div>
                        <p class="text-xs font-bold text-app-muted uppercase tracking-widest mb-3">Razorpay</p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="flex flex-col gap-1.5">
                                <label for="razorpay_key" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Key ID</label>
                                <input id="razorpay_key" name="razorpay_key" :value="settings.razorpay_key ?? ''" placeholder="rzp_live_..." class="bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors" />
                                <InputError :message="errors.razorpay_key" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label for="razorpay_secret" class="text-xs font-semibold text-app-muted uppercase tracking-wider">Key secret</label>
                                <div class="relative">
                                    <input
                                        id="razorpay_secret"
                                        name="razorpay_secret"
                                        type="password"
                                        :placeholder="settings.razorpay_secret_configured ? '● Configured — leave blank to keep' : 'Enter secret'"
                                        class="w-full bg-app-elevated border border-app rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-[#f7600d] focus:ring-1 focus:ring-[#f7600d]/20 transition-colors"
                                    />
                                    <span v-if="settings.razorpay_secret_configured" class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-400 text-xs">✓</span>
                                </div>
                                <InputError :message="errors.razorpay_secret" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    :disabled="processing"
                    class="flex items-center gap-2 bg-[#f7600d] hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed text-app text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors"
                >
                    <svg v-if="processing" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Save third-party settings
                </button>
            </div>
        </Form>
    </div>
</template>
