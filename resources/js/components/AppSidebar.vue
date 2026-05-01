<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    BriefcaseBusiness,
    CircleDollarSign,
    FolderGit2,
    Globe,
    GraduationCap,
    LayoutGrid,
    Library,
    ListChecks,
    Settings,
    ShieldCheck,
    UsersRound,
    Wrench,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as adminRolesIndex } from '@/routes/admin/roles';
import { app as appSettings, landing as landingCms, thirdParty } from '@/routes/admin/settings';
import { index as contactsIndex } from '@/routes/app/contacts';
import { index as coursesIndex } from '@/routes/app/courses';
import { index as dealsIndex } from '@/routes/app/deals';
import { index as libraryIndex } from '@/routes/app/library';
import { index as projectsIndex } from '@/routes/app/projects';
import { index as tasksIndex } from '@/routes/app/tasks';
import type { NavItem } from '@/types';

const page = usePage<{ auth: { isAdmin: boolean } }>();
const isAdmin = computed(() => page.props.auth?.isAdmin ?? false);

const mainNavItems: NavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    { title: 'Projects', href: projectsIndex(), icon: BriefcaseBusiness },
    { title: 'Tasks', href: tasksIndex(), icon: ListChecks },
    { title: 'Library', href: libraryIndex(), icon: Library },
    { title: 'Courses', href: coursesIndex(), icon: GraduationCap },
];

const crmNavItems: NavItem[] = [
    { title: 'Contacts', href: contactsIndex(), icon: UsersRound },
    { title: 'Deals', href: dealsIndex(), icon: CircleDollarSign },
];

const adminNavItems: NavItem[] = [
    { title: 'App settings', href: appSettings(), icon: Settings },
    { title: 'Third-party', href: thirdParty(), icon: Wrench },
    { title: 'Landing CMS', href: landingCms(), icon: Globe },
    { title: 'Roles', href: adminRolesIndex(), icon: ShieldCheck },
];

const footerNavItems: NavItem[] = [
    { title: 'Repository', href: 'https://github.com/laravel/vue-starter-kit', icon: FolderGit2 },
    { title: 'Documentation', href: 'https://laravel.com/docs/starter-kits#vue', icon: BookOpen },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <NavMain :items="crmNavItems" label="CRM" />
            <NavMain v-if="isAdmin" :items="adminNavItems" label="Admin" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
