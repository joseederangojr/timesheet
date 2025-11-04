import { Button } from '@/components/ui/button';
import { useAuthUser } from '@/hooks/use-auth-user';
import { useSidebar } from '@/hooks/use-sidebar';
import { cn } from '@/lib/utils';
import { Form, Link } from '@inertiajs/react';
import {
    Building2,
    ChevronLeft,
    ChevronRight,
    Home,
    LogOut,
    Users,
    type LucideIcon,
} from 'lucide-react';
import { type ReactNode } from 'react';

interface NavigationItem {
    label: string;
    href: string;
    icon: LucideIcon;
    active?: boolean;
}

interface AdminLayoutProps {
    children: ReactNode;
    currentPath?: string;
}

const navigationItems: NavigationItem[] = [
    {
        label: 'Dashboard',
        href: '/admin/dashboard',
        icon: Home,
    },
    {
        label: 'Users',
        href: '/admin/users',
        icon: Users,
    },
    {
        label: 'Clients',
        href: '/admin/clients',
        icon: Building2,
    },
    {
        label: 'Employments',
        href: '/admin/employments',
        icon: Users,
    },
];

export function AdminLayout({ children, currentPath }: AdminLayoutProps) {
    const user = useAuthUser();
    const { sidebarCollapsed, toggleSidebar } = useSidebar();

    const itemsWithActiveState = navigationItems.map((item) => ({
        ...item,
        active: currentPath === item.href,
    }));

    return (
        <div className="flex min-h-screen bg-background">
            <AdminSidebar
                collapsed={sidebarCollapsed}
                onToggle={toggleSidebar}
                navigationItems={itemsWithActiveState}
            />
            <div className="flex-1">
                {user && <AdminHeader user={user} />}

                <main className="p-6">{children}</main>
            </div>
        </div>
    );
}

interface AdminSidebarProps {
    collapsed: boolean;
    onToggle: () => void;
    navigationItems: NavigationItem[];
}

function AdminSidebar({
    collapsed,
    onToggle,
    navigationItems,
}: AdminSidebarProps) {
    return (
        <div
            className={cn(
                'flex flex-col border-r bg-card transition-all duration-300',
                collapsed ? 'w-16' : 'w-64',
            )}
        >
            <SidebarHeader collapsed={collapsed} onToggle={onToggle} />
            <SidebarNavigation collapsed={collapsed} items={navigationItems} />
            <SidebarFooter collapsed={collapsed} />
        </div>
    );
}

interface SidebarHeaderProps {
    collapsed: boolean;
    onToggle: () => void;
}

function SidebarHeader({ collapsed, onToggle }: SidebarHeaderProps) {
    return (
        <div className="flex h-16 items-center justify-between border-b px-4">
            {!collapsed && <h1 className="text-xl font-bold">Admin</h1>}
            <Button
                variant="ghost"
                size="sm"
                onClick={onToggle}
                className="h-8 w-8 p-0"
                aria-label={collapsed ? 'Expand sidebar' : 'Collapse sidebar'}
            >
                {collapsed ? (
                    <ChevronRight className="h-4 w-4" />
                ) : (
                    <ChevronLeft className="h-4 w-4" />
                )}
            </Button>
        </div>
    );
}

interface SidebarNavigationProps {
    collapsed: boolean;
    items: NavigationItem[];
}

function SidebarNavigation({ collapsed, items }: SidebarNavigationProps) {
    return (
        <nav className="flex-1 space-y-2 p-4">
            {items.map((item) => (
                <Link
                    key={item.href}
                    href={item.href}
                    className={cn(
                        'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground',
                        item.active && 'bg-accent text-accent-foreground',
                    )}
                >
                    <item.icon className="h-4 w-4" />
                    {!collapsed && <span>{item.label}</span>}
                </Link>
            ))}
        </nav>
    );
}

interface SidebarFooterProps {
    collapsed: boolean;
}

function SidebarFooter({ collapsed }: SidebarFooterProps) {
    return (
        <div className="border-t p-4">
            <Form action="/auth/session" method="delete">
                {({ processing }) => (
                    <Button
                        type="submit"
                        variant="ghost"
                        disabled={processing}
                        className={cn(
                            'h-10 w-full justify-start gap-3',
                            collapsed && 'px-2',
                        )}
                    >
                        <LogOut className="h-4 w-4" />
                        {!collapsed && (
                            <span>
                                {processing ? 'Signing out...' : 'Sign Out'}
                            </span>
                        )}
                    </Button>
                )}
            </Form>
        </div>
    );
}

interface AdminHeaderProps {
    user: { name: string; email: string };
}

function AdminHeader({ user }: AdminHeaderProps) {
    return (
        <header className="flex h-16 items-center justify-between border-b bg-card px-6">
            <div className="flex items-center gap-4">
                <h2 className="text-lg font-semibold">Admin Panel</h2>
            </div>
            <div className="flex items-center gap-4">
                <span className="text-sm text-muted-foreground">
                    Welcome, {user.name}
                </span>
            </div>
        </header>
    );
}
