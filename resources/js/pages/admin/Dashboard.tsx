import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useSidebar } from '@/hooks/use-sidebar';
import { cn } from '@/lib/utils';
import { Form, Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight, Home, LogOut, Users } from 'lucide-react';

interface AdminDashboardProps {
    greeting?: string;
    auth: {
        user: {
            name: string;
            email: string;
        };
    };
}

export default function AdminDashboard({
    greeting,
    auth,
}: AdminDashboardProps) {
    const { sidebarCollapsed, toggleSidebar } = useSidebar();

    return (
        <div className="flex min-h-screen bg-background">
            {/* Sidebar */}
            <div
                className={cn(
                    'flex flex-col border-r bg-card transition-all duration-300',
                    sidebarCollapsed ? 'w-16' : 'w-64',
                )}
            >
                {/* Sidebar Header */}
                <div className="flex h-16 items-center justify-between border-b px-4">
                    {!sidebarCollapsed && (
                        <h1 className="text-xl font-bold">Laravel</h1>
                    )}
                    <Button
                        variant="ghost"
                        size="sm"
                        onClick={toggleSidebar}
                        className="h-8 w-8 p-0"
                    >
                        {sidebarCollapsed ? (
                            <ChevronRight className="h-4 w-4" />
                        ) : (
                            <ChevronLeft className="h-4 w-4" />
                        )}
                    </Button>
                </div>

                {/* Sidebar Navigation */}
                <nav className="flex-1 space-y-2 p-4">
                    <Link
                        href="/admin/dashboard"
                        className={cn(
                            'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground',
                            'bg-accent text-accent-foreground', // Active state for dashboard
                        )}
                    >
                        <Home className="h-4 w-4" />
                        {!sidebarCollapsed && <span>Dashboard</span>}
                    </Link>
                    <Link
                        href="/admin/users"
                        className={cn(
                            'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground',
                        )}
                    >
                        <Users className="h-4 w-4" />
                        {!sidebarCollapsed && <span>Users</span>}
                    </Link>
                </nav>

                {/* Sidebar Footer */}
                <div className="border-t p-4">
                    <Form action="/auth/session" method="delete">
                        {({ processing }) => (
                            <Button
                                type="submit"
                                variant="ghost"
                                disabled={processing}
                                className={cn(
                                    'h-10 w-full justify-start gap-3',
                                    sidebarCollapsed && 'px-2',
                                )}
                            >
                                <LogOut className="h-4 w-4" />
                                {!sidebarCollapsed && (
                                    <span>
                                        {processing
                                            ? 'Signing out...'
                                            : 'Sign Out'}
                                    </span>
                                )}
                            </Button>
                        )}
                    </Form>
                </div>
            </div>

            {/* Main Content */}
            <div className="flex-1">
                {/* Top Bar */}
                <header className="flex h-16 items-center justify-between border-b bg-card px-6">
                    <div className="flex items-center gap-4">
                        <h2 className="text-lg font-semibold">
                            Admin Dashboard
                        </h2>
                    </div>
                    <div className="flex items-center gap-4">
                        <span className="text-sm text-muted-foreground">
                            Welcome, {auth.user.name}
                        </span>
                    </div>
                </header>

                {/* Dashboard Content */}
                <main className="p-6">
                    <div className="mb-6">
                        <h1 className="text-3xl font-bold">
                            {greeting || `Hello, ${auth.user.name}`}
                        </h1>
                        <p className="text-muted-foreground">
                            Welcome to the admin dashboard
                        </p>
                    </div>

                    {/* Dashboard Stats/Cards - Empty for now as requested */}
                    <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <Card>
                            <CardHeader>
                                <CardTitle>Total Users</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-2xl font-bold">--</p>
                                <p className="text-sm text-muted-foreground">
                                    User management coming soon
                                </p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>System Status</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-2xl font-bold text-green-600">
                                    Online
                                </p>
                                <p className="text-sm text-muted-foreground">
                                    All systems operational
                                </p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Quick Actions</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-sm text-muted-foreground">
                                    Admin tools coming soon
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                </main>
            </div>
        </div>
    );
}
