import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useSidebar } from '@/hooks/use-sidebar';
import { cn } from '@/lib/utils';
import { Form, Link, router } from '@inertiajs/react';
import {
    ChevronLeft,
    ChevronRight,
    Home,
    LogOut,
    Search,
    Users,
} from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    roles: Array<{
        id: number;
        name: string;
    }>;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedUsers {
    data: User[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

interface AdminUsersIndexProps {
    users: PaginatedUsers;
    filters: {
        search?: string;
    };
    auth: {
        user: {
            name: string;
            email: string;
        };
    };
}

export default function AdminUsersIndex({
    users,
    filters,
    auth,
}: AdminUsersIndexProps) {
    const { sidebarCollapsed, toggleSidebar } = useSidebar();
    const [searchTerm, setSearchTerm] = useState(filters.search || '');

    const handleSearch = useCallback(() => {
        router.get(
            '/admin/users',
            { search: searchTerm },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, [searchTerm]);

    const clearSearch = () => {
        setSearchTerm('');
        router.get('/admin/users', {}, { preserveState: true, replace: true });
    };

    useEffect(() => {
        const timeoutId = setTimeout(() => {
            if (searchTerm !== filters.search) {
                handleSearch();
            }
        }, 300);

        return () => clearTimeout(timeoutId);
    }, [searchTerm, filters.search, handleSearch]);

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    const getInitials = (name: string) => {
        return name
            .split(' ')
            .map((word) => word[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    const getRoleNames = (roles: User['roles']) => {
        return roles.map((role) => role.name).join(', ') || 'No roles';
    };

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
                        )}
                    >
                        <Home className="h-4 w-4" />
                        {!sidebarCollapsed && <span>Dashboard</span>}
                    </Link>
                    <Link
                        href="/admin/users"
                        className={cn(
                            'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground',
                            'bg-accent text-accent-foreground', // Active state for users
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
                        <h2 className="text-lg font-semibold">Users</h2>
                    </div>
                    <div className="flex items-center gap-4">
                        <span className="text-sm text-muted-foreground">
                            Welcome, {auth.user.name}
                        </span>
                    </div>
                </header>

                {/* Users Content */}
                <main className="p-6">
                    <div className="mb-6 flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold">Users</h1>
                            <p className="text-muted-foreground">
                                Manage system users and their roles
                            </p>
                        </div>
                    </div>

                    {/* Search and Filters */}
                    <Card className="mb-6">
                        <CardHeader>
                            <CardTitle>Search Users</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="flex gap-4">
                                <div className="relative flex-1">
                                    <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                    <Input
                                        placeholder="Search by name or email..."
                                        value={searchTerm}
                                        onChange={(e) =>
                                            setSearchTerm(e.target.value)
                                        }
                                        className="pl-10"
                                    />
                                </div>
                                {searchTerm && (
                                    <Button
                                        variant="outline"
                                        onClick={clearSearch}
                                    >
                                        Clear
                                    </Button>
                                )}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Users Table */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Users ({users.total} total)</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>User</TableHead>
                                        <TableHead>Email</TableHead>
                                        <TableHead>Roles</TableHead>
                                        <TableHead>Verified</TableHead>
                                        <TableHead>Joined</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {users.data.length === 0 ? (
                                        <TableRow>
                                            <TableCell
                                                colSpan={5}
                                                className="text-center text-muted-foreground"
                                            >
                                                {filters.search
                                                    ? 'No users found matching your search.'
                                                    : 'No users found.'}
                                            </TableCell>
                                        </TableRow>
                                    ) : (
                                        users.data.map((user) => (
                                            <TableRow key={user.id}>
                                                <TableCell>
                                                    <div className="flex items-center gap-3">
                                                        <Avatar className="h-8 w-8">
                                                            <AvatarFallback className="text-xs">
                                                                {getInitials(
                                                                    user.name,
                                                                )}
                                                            </AvatarFallback>
                                                        </Avatar>
                                                        <div>
                                                            <div className="font-medium">
                                                                {user.name}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </TableCell>
                                                <TableCell className="text-muted-foreground">
                                                    {user.email}
                                                </TableCell>
                                                <TableCell>
                                                    <span className="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                                                        {getRoleNames(
                                                            user.roles,
                                                        )}
                                                    </span>
                                                </TableCell>
                                                <TableCell>
                                                    {user.email_verified_at ? (
                                                        <span className="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 dark:bg-green-900/20 dark:text-green-300">
                                                            Verified
                                                        </span>
                                                    ) : (
                                                        <span className="inline-flex items-center rounded-full bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-300">
                                                            Unverified
                                                        </span>
                                                    )}
                                                </TableCell>
                                                <TableCell className="text-muted-foreground">
                                                    {formatDate(
                                                        user.created_at,
                                                    )}
                                                </TableCell>
                                            </TableRow>
                                        ))
                                    )}
                                </TableBody>
                            </Table>

                            {/* Pagination */}
                            {users.last_page > 1 && (
                                <div className="mt-4 flex items-center justify-between">
                                    <div className="text-sm text-muted-foreground">
                                        Showing {users.data.length} of{' '}
                                        {users.total} users
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        {users.links.map((link, index) => {
                                            if (
                                                link.label ===
                                                '&amp;laquo; Previous'
                                            ) {
                                                return (
                                                    <Button
                                                        key={index}
                                                        variant="outline"
                                                        size="sm"
                                                        disabled={!link.url}
                                                        onClick={() =>
                                                            link.url &&
                                                            router.get(link.url)
                                                        }
                                                    >
                                                        Previous
                                                    </Button>
                                                );
                                            }
                                            if (
                                                link.label ===
                                                'Next &amp;raquo;'
                                            ) {
                                                return (
                                                    <Button
                                                        key={index}
                                                        variant="outline"
                                                        size="sm"
                                                        disabled={!link.url}
                                                        onClick={() =>
                                                            link.url &&
                                                            router.get(link.url)
                                                        }
                                                    >
                                                        Next
                                                    </Button>
                                                );
                                            }
                                            if (
                                                !isNaN(Number(link.label)) ||
                                                link.label === '...'
                                            ) {
                                                return (
                                                    <Button
                                                        key={index}
                                                        variant={
                                                            link.active
                                                                ? 'default'
                                                                : 'outline'
                                                        }
                                                        size="sm"
                                                        disabled={
                                                            !link.url ||
                                                            link.label === '...'
                                                        }
                                                        onClick={() =>
                                                            link.url &&
                                                            router.get(link.url)
                                                        }
                                                    >
                                                        {link.label}
                                                    </Button>
                                                );
                                            }
                                            return null;
                                        })}
                                    </div>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </main>
            </div>
        </div>
    );
}
