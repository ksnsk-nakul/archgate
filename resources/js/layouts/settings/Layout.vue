<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import { app as adminApp, landing as adminLanding, thirdParty as adminThirdParty } from '@/routes/admin/settings';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
import type { NavItem } from '@/types';

const page = usePage<{ auth: { isAdmin: boolean } }>();
const isAdmin = computed(() => page.props.auth?.isAdmin ?? false);

const sidebarNavItems: NavItem[] = [
    { title: 'Profile', href: editProfile() },
    { title: 'Security', href: editSecurity() },
    { title: 'Appearance', href: editAppearance() },
];

const adminNavItems: NavItem[] = [
    { title: 'App Settings', href: adminApp() },
    { title: 'Third-party', href: adminThirdParty() },
    { title: 'Landing CMS', href: adminLanding() },
];

const { isCurrentOrParentUrl } = useCurrentUrl();
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            title="Settings"
            description="Manage your profile and account settings"
        />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0" aria-label="Settings">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :class="['w-full justify-start', { 'bg-muted': isCurrentOrParentUrl(item.href) }]"
                        as-child
                    >
                        <Link :href="item.href">
                            <component :is="item.icon" class="h-4 w-4" />
                            {{ item.title }}
                        </Link>
                    </Button>

                    <template v-if="isAdmin">
                        <div class="pt-4 pb-1 px-3">
                            <p class="text-xs font-semibold text-muted-foreground/60 uppercase tracking-wider">Admin</p>
                        </div>
                        <Button
                            v-for="item in adminNavItems"
                            :key="toUrl(item.href)"
                            variant="ghost"
                            :class="['w-full justify-start', { 'bg-muted': isCurrentOrParentUrl(item.href) }]"
                            as-child
                        >
                            <Link :href="item.href">
                                <component :is="item.icon" class="h-4 w-4" />
                                {{ item.title }}
                            </Link>
                        </Button>
                    </template>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
