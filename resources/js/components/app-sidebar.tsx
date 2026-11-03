import { Link, usePage } from '@inertiajs/react';
import { Inbox, LayoutGrid, Plus } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
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
import type { Auth, NavItem, UserRole } from '@/types';
import { useTranslations } from '@/hooks/use-translations';

const mainNavItems = (
    role: UserRole,
    labels: ReturnType<typeof useTranslations>['shell'],
): NavItem[] => [
    {
        title: labels.dashboard,
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title:
            role === 'client'
                ? labels.my_tickets
                : role === 'agent'
                  ? labels.assigned_tickets
                  : labels.all_tickets,
        href: '/tickets',
        icon: Inbox,
    },
    {
        title: labels.new_ticket,
        href: '/tickets/create',
        icon: Plus,
        roles: ['client', 'agent'],
    },
];

export function AppSidebar() {
    const { auth } = usePage<{ auth: Auth }>().props;
    const translations = useTranslations();

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain
                    items={mainNavItems(auth.user.role, translations.shell)}
                />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
