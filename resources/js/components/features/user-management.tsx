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
import { router } from '@inertiajs/react';
import { ArrowDown, ArrowUp, ArrowUpDown, Search } from 'lucide-react';
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

interface UserSearchProps {
    searchTerm: string;
    onSearchChange: (value: string) => void;
    onClearSearch: () => void;
}

export function UserSearch({
    searchTerm,
    onSearchChange,
    onClearSearch,
}: UserSearchProps) {
    return (
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
                            onChange={(e) => onSearchChange(e.target.value)}
                            className="pl-10"
                        />
                    </div>
                    {searchTerm && (
                        <Button variant="outline" onClick={onClearSearch}>
                            Clear
                        </Button>
                    )}
                </div>
            </CardContent>
        </Card>
    );
}

interface UserTableProps {
    users: PaginatedUsers;
    hasSearchFilter: boolean;
    sortBy?: string;
    sortDirection?: 'asc' | 'desc';
    onSort?: (field: string) => void;
}

interface SortableTableHeadProps {
    field: string;
    label: string;
    sortBy?: string;
    sortDirection?: 'asc' | 'desc';
    onSort?: (field: string) => void;
}

function SortableTableHead({
    field,
    label,
    sortBy,
    sortDirection,
    onSort,
}: SortableTableHeadProps) {
    const isActive = sortBy === field;
    const Icon = isActive
        ? sortDirection === 'asc'
            ? ArrowUp
            : ArrowDown
        : ArrowUpDown;

    return (
        <Button
            variant="ghost"
            size="sm"
            className="h-auto p-0 font-medium hover:bg-transparent"
            onClick={() => onSort?.(field)}
        >
            {label}
            <Icon className="ml-2 h-4 w-4" />
        </Button>
    );
}

export function UserTable({
    users,
    hasSearchFilter,
    sortBy,
    sortDirection,
    onSort,
}: UserTableProps) {
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

    return (
        <Card>
            <CardHeader>
                <CardTitle>Users ({users.total} total)</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>
                                <SortableTableHead
                                    field="name"
                                    label="User"
                                    sortBy={sortBy}
                                    sortDirection={sortDirection}
                                    onSort={onSort}
                                />
                            </TableHead>
                            <TableHead>
                                <SortableTableHead
                                    field="email"
                                    label="Email"
                                    sortBy={sortBy}
                                    sortDirection={sortDirection}
                                    onSort={onSort}
                                />
                            </TableHead>
                            <TableHead>Roles</TableHead>
                            <TableHead>
                                <SortableTableHead
                                    field="email_verified_at"
                                    label="Verified"
                                    sortBy={sortBy}
                                    sortDirection={sortDirection}
                                    onSort={onSort}
                                />
                            </TableHead>
                            <TableHead>
                                <SortableTableHead
                                    field="created_at"
                                    label="Joined"
                                    sortBy={sortBy}
                                    sortDirection={sortDirection}
                                    onSort={onSort}
                                />
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {users.data.length === 0 ? (
                            <TableRow>
                                <TableCell
                                    colSpan={5}
                                    className="text-center text-muted-foreground"
                                >
                                    {hasSearchFilter
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
                                                    {getInitials(user.name)}
                                                </AvatarFallback>
                                            </Avatar>
                                            <div className="font-medium">
                                                {user.name}
                                            </div>
                                        </div>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {user.email}
                                    </TableCell>
                                    <TableCell>
                                        <UserRoleBadge roles={user.roles} />
                                    </TableCell>
                                    <TableCell>
                                        <UserVerificationBadge
                                            verified={!!user.email_verified_at}
                                        />
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {formatDate(user.created_at)}
                                    </TableCell>
                                </TableRow>
                            ))
                        )}
                    </TableBody>
                </Table>

                {users.last_page > 1 && <UserTablePagination users={users} />}
            </CardContent>
        </Card>
    );
}

interface UserRoleBadgeProps {
    roles: User['roles'];
}

function UserRoleBadge({ roles }: UserRoleBadgeProps) {
    const roleNames = roles.map((role) => role.name).join(', ') || 'No roles';

    return (
        <span className="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
            {roleNames}
        </span>
    );
}

interface UserVerificationBadgeProps {
    verified: boolean;
}

function UserVerificationBadge({ verified }: UserVerificationBadgeProps) {
    if (verified) {
        return (
            <span className="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 dark:bg-green-900/20 dark:text-green-300">
                Verified
            </span>
        );
    }

    return (
        <span className="inline-flex items-center rounded-full bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-300">
            Unverified
        </span>
    );
}

interface UserTablePaginationProps {
    users: PaginatedUsers;
}

function UserTablePagination({ users }: UserTablePaginationProps) {
    return (
        <div className="mt-4 flex items-center justify-between">
            <div className="text-sm text-muted-foreground">
                Showing {users.data.length} of {users.total} users
            </div>
            <div className="flex items-center space-x-2">
                {users.links.map((link, index) => {
                    if (link.label === '&amp;laquo; Previous') {
                        return (
                            <Button
                                key={index}
                                variant="outline"
                                size="sm"
                                disabled={!link.url}
                                onClick={() => link.url && router.get(link.url)}
                            >
                                Previous
                            </Button>
                        );
                    }
                    if (link.label === 'Next &amp;raquo;') {
                        return (
                            <Button
                                key={index}
                                variant="outline"
                                size="sm"
                                disabled={!link.url}
                                onClick={() => link.url && router.get(link.url)}
                            >
                                Next
                            </Button>
                        );
                    }
                    if (!isNaN(Number(link.label)) || link.label === '...') {
                        return (
                            <Button
                                key={index}
                                variant={link.active ? 'default' : 'outline'}
                                size="sm"
                                disabled={!link.url || link.label === '...'}
                                onClick={() => link.url && router.get(link.url)}
                            >
                                {link.label}
                            </Button>
                        );
                    }
                    return null;
                })}
            </div>
        </div>
    );
}

interface UseUserSearchProps {
    initialSearch?: string;
}

export function useUserSearch({ initialSearch = '' }: UseUserSearchProps = {}) {
    const [searchTerm, setSearchTerm] = useState(initialSearch);

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
            if (searchTerm !== initialSearch) {
                handleSearch();
            }
        }, 300);

        return () => clearTimeout(timeoutId);
    }, [searchTerm, initialSearch, handleSearch]);

    return {
        searchTerm,
        setSearchTerm,
        clearSearch,
    };
}

interface UseUserSortProps {
    initialSortBy?: string;
    initialSortDirection?: 'asc' | 'desc';
    currentSearch?: string;
}

export function useUserSort({
    initialSortBy = 'created_at',
    initialSortDirection = 'desc',
    currentSearch,
}: UseUserSortProps = {}) {
    const [sortBy, setSortBy] = useState(initialSortBy);
    const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>(
        initialSortDirection,
    );

    const handleSort = useCallback(
        (field: string) => {
            const newDirection =
                sortBy === field && sortDirection === 'asc' ? 'desc' : 'asc';
            setSortBy(field);
            setSortDirection(newDirection);

            const params: Record<string, string> = {
                sort_by: field,
                sort_direction: newDirection,
            };

            if (currentSearch) {
                params.search = currentSearch;
            }

            router.get('/admin/users', params, {
                preserveState: true,
                replace: true,
            });
        },
        [sortBy, sortDirection, currentSearch],
    );

    return {
        sortBy,
        sortDirection,
        handleSort,
    };
}
