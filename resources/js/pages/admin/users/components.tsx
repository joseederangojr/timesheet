import { UserForm } from '@/components/admin/user-form';
import { ComboboxFilter } from '@/components/combobox-filter';
import { DataTableColumnHeader } from '@/components/data-table-column-header';
import { DataTablePagination } from '@/components/data-table-pagination';
import { DataTableViewOptions } from '@/components/data-table-view-options';
import { SearchInput } from '@/components/search-input';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    DataTable,
    DataTableContent,
    DataTableFooter,
    DataTableHeader,
    DataTableRoot,
    useDataTable,
} from '@/components/ui/data-table';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { Paginated } from '@/types';
import { Form, router, usePage } from '@inertiajs/react';
import {
    ColumnDef,
    PaginationState,
    SortingState,
} from '@tanstack/react-table';
import { Check, ChevronsUpDown, Edit, Plus, X } from 'lucide-react';
import * as React from 'react';

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

interface UsersFilters {
    search?: string;
    sort_by?: string;
    sort_direction?: string;
    role?: string;
    verified?: string;
    page?: string;
    per_page?: string;
}

export function UserSearchInput() {
    const { props } = usePage<{ filters: UsersFilters }>();
    const handleValueChange = (value?: string) =>
        onAdminUsersFilterChange({
            ...props.filters,
            search: value,
        });
    return (
        <SearchInput
            value={props.filters.search ?? ''}
            placeholder="Search by name or email"
            onValueChange={handleValueChange}
        />
    );
}

export function onAdminUsersFilterChange(filters: UsersFilters) {
    router.get(
        '/admin/users',
        { ...filters },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

export function UserRoleFilter() {
    const {
        props: { filters },
    } = usePage<{ filters: UsersFilters }>();
    const value = filters?.role ?? '';

    const handleValueChange = (value?: string) => {
        onAdminUsersFilterChange({ ...filters, role: value });
    };

    const roles = [
        { value: 'admin', label: 'Admin' },
        { value: 'employee', label: 'Employee' },
    ];

    return (
        <ComboboxFilter
            options={roles}
            value={value}
            onValueChange={handleValueChange}
            placeholder="Role"
        />
    );
}

export function UserVerifiedFilter() {
    const {
        props: { filters },
    } = usePage<{ filters: UsersFilters }>();
    const value = filters?.verified ?? '';

    const handleValueChange = (value?: string) => {
        onAdminUsersFilterChange({ ...filters, verified: value });
    };

    const status = [
        { value: 'verified', label: 'Verified' },
        { value: 'unverified', label: 'Unverified' },
    ];

    return (
        <ComboboxFilter
            options={status}
            value={value}
            onValueChange={handleValueChange}
            placeholder="Verified"
        />
    );
}

const userColumns: ColumnDef<User>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return <DataTableColumnHeader column={column} title="Name" />;
        },
        cell: ({ row }) => {
            const user = row.original;
            const getInitials = (name: string) => {
                return name
                    .split(' ')
                    .map((word) => word[0])
                    .join('')
                    .toUpperCase()
                    .slice(0, 2);
            };
            return (
                <div className="flex items-center gap-3">
                    <Avatar className="h-8 w-8">
                        <AvatarFallback className="text-xs">
                            {getInitials(user.name)}
                        </AvatarFallback>
                    </Avatar>
                    <div className="font-medium">{user.name}</div>
                </div>
            );
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) => {
            return <DataTableColumnHeader column={column} title="Email" />;
        },
        cell: ({ row }) => (
            <div className="text-muted-foreground">{row.getValue('email')}</div>
        ),
    },
    {
        accessorKey: 'roles',
        header: 'Roles',
        cell: ({ row }) => <UserRoleBadge roles={row.original.roles} />,
    },
    {
        accessorKey: 'email_verified_at',
        header: ({ column }) => {
            return <DataTableColumnHeader column={column} title="Verified" />;
        },
        cell: ({ row }) => (
            <UserVerificationBadge
                verified={!!row.getValue('email_verified_at')}
            />
        ),
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) => {
            return <DataTableColumnHeader column={column} title="Joined At" />;
        },
        cell: ({ row }) => {
            const formatDate = (dateString: string) => {
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                });
            };
            return (
                <div className="text-muted-foreground">
                    {formatDate(row.getValue('created_at'))}
                </div>
            );
        },
    },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => <UserActionsCell user={row.original} />,
    },
];

export function AdminUsersDataTable() {
    'use no memo';
    const {
        props: { users, filters },
    } = usePage<{ filters: UsersFilters; users: Paginated<User> }>();

    const handleSortingChange = (sorting?: SortingState) => {
        if (!sorting?.length) return;
        const [sort] = sorting;
        onAdminUsersFilterChange({
            ...filters,
            sort_by: sort.id,
            sort_direction: sort.desc ? 'desc' : 'asc',
        });
    };

    const sorting = React.useMemo(() => {
        return filters.sort_by
            ? [
                  {
                      id: filters.sort_by,
                      desc: filters.sort_direction === 'desc',
                  },
              ]
            : [];
    }, [filters.sort_by, filters.sort_direction]);

    const handlePaginationChange = (pagination?: PaginationState) => {
        if (!pagination) return;
        const page = (pagination?.pageIndex || 0) + 1;
        const perPage = pagination?.pageSize || 15;
        onAdminUsersFilterChange({
            ...filters,
            page: page.toString(),
            per_page: perPage.toString(),
        });
    };

    const table = useDataTable({
        columns: userColumns,
        data: users.data,
        pagination: {
            current_page: users.current_page,
            last_page: users.last_page,
            per_page: users.per_page,
            total: users.total,
            links: users.links,
        },
        onPaginationChange: handlePaginationChange,
        sorting,
        onSortingChange: handleSortingChange,
    });

    return (
        <Card>
            <CardHeader>
                <CardTitle>Users ({users.total} total)</CardTitle>
            </CardHeader>
            <CardContent>
                <DataTableRoot>
                    <DataTableHeader>
                        <div className="flex flex-col gap-4 md:flex-row md:items-end">
                            <div className="flex-1">
                                <UserSearchInput />
                            </div>
                            <UserRoleFilter />
                            <UserVerifiedFilter />
                            <DataTableViewOptions table={table} />
                        </div>
                    </DataTableHeader>
                    <DataTableContent>
                        <DataTable table={table} />
                    </DataTableContent>
                    <DataTableFooter>
                        <DataTablePagination table={table} />
                    </DataTableFooter>
                </DataTableRoot>
            </CardContent>
        </Card>
    );
}

interface UserRoleBadgeProps {
    roles: User['roles'];
}

function UserRoleBadge({ roles }: UserRoleBadgeProps) {
    if (roles.length === 0) {
        return (
            <span className="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 dark:bg-gray-900/20 dark:text-gray-300">
                No roles
            </span>
        );
    }

    return (
        <div className="flex flex-wrap gap-1">
            {roles.map((role) => (
                <span
                    key={role.id}
                    className="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-300"
                >
                    {role.name}
                </span>
            ))}
        </div>
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

interface UserActionsCellProps {
    user: User;
}

function UserActionsCell({ user }: UserActionsCellProps) {
    return (
        <div className="flex items-center gap-2">
            <UserEditDialog user={user} />
        </div>
    );
}

interface UserEditDialogProps {
    user: User;
}

function UserEditDialog({ user }: UserEditDialogProps) {
    const [open, setOpen] = React.useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="ghost" size="sm">
                    <Edit className="h-4 w-4" />
                </Button>
            </DialogTrigger>
            <DialogContent className="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Edit User</DialogTitle>
                </DialogHeader>
                <UserEditForm user={user} onSuccess={() => setOpen(false)} />
            </DialogContent>
        </Dialog>
    );
}

interface UserEditFormProps {
    user: User;
    onSuccess?: () => void;
}

function UserEditForm({ user, onSuccess }: UserEditFormProps) {
    const { props } = usePage<{ roles: Role[] }>();
    const [selectedRoles, setSelectedRoles] = React.useState<string[]>(
        user.roles.map((role) => role.name),
    );
    const [open, setOpen] = React.useState(false);

    const toggleRole = (roleName: string) => {
        setSelectedRoles((prev) =>
            prev.includes(roleName)
                ? prev.filter((r) => r !== roleName)
                : [...prev, roleName],
        );
    };

    const removeRole = (roleName: string) => {
        setSelectedRoles((prev) => prev.filter((r) => r !== roleName));
    };

    return (
        <Form
            method="put"
            action={`/admin/users/${user.id}`}
            onSuccess={() => {
                onSuccess?.();
            }}
            className="space-y-6"
        >
            {({ errors }) => (
                <>
                    <div>
                        <Label htmlFor="name">Name</Label>
                        <Input
                            id="name"
                            name="name"
                            placeholder="Enter user name"
                            defaultValue={user.name}
                            required
                        />
                        {errors.name && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.name}
                            </p>
                        )}
                    </div>

                    <div>
                        <Label htmlFor="email">Email</Label>
                        <Input
                            id="email"
                            name="email"
                            type="email"
                            placeholder="Enter email address"
                            defaultValue={user.email}
                            required
                        />
                        {errors.email && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.email}
                            </p>
                        )}
                    </div>

                    <div>
                        <Label>Roles</Label>
                        <Popover open={open} onOpenChange={setOpen}>
                            <PopoverTrigger asChild>
                                <Button
                                    variant="outline"
                                    role="combobox"
                                    aria-expanded={open}
                                    className="w-full justify-between"
                                >
                                    {selectedRoles.length > 0
                                        ? `${selectedRoles.length} role(s) selected`
                                        : 'Select roles...'}
                                    <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent className="w-full p-0">
                                <Command>
                                    <CommandInput placeholder="Search roles..." />
                                    <CommandList>
                                        <CommandEmpty>
                                            No roles found.
                                        </CommandEmpty>
                                        <CommandGroup>
                                            {props.roles.map((role) => (
                                                <CommandItem
                                                    key={role.id}
                                                    onSelect={() =>
                                                        toggleRole(role.name)
                                                    }
                                                >
                                                    <Check
                                                        className={cn(
                                                            'mr-2 h-4 w-4',
                                                            selectedRoles.includes(
                                                                role.name,
                                                            )
                                                                ? 'opacity-100'
                                                                : 'opacity-0',
                                                        )}
                                                    />
                                                    {role.name}
                                                </CommandItem>
                                            ))}
                                        </CommandGroup>
                                    </CommandList>
                                </Command>
                            </PopoverContent>
                        </Popover>
                        {selectedRoles.length > 0 && (
                            <div className="mt-2 flex flex-wrap gap-2">
                                {selectedRoles.map((roleName) => (
                                    <Badge
                                        key={roleName}
                                        variant="secondary"
                                        className="flex items-center gap-1"
                                    >
                                        {roleName}
                                        <button
                                            type="button"
                                            onClick={() => removeRole(roleName)}
                                            className="ml-1 rounded-full p-0.5 hover:bg-secondary-foreground/20"
                                        >
                                            <X className="h-3 w-3" />
                                        </button>
                                    </Badge>
                                ))}
                            </div>
                        )}
                        {errors.roles && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.roles}
                            </p>
                        )}
                        {/* Hidden inputs for selected roles */}
                        {selectedRoles.map((roleName) => (
                            <input
                                key={roleName}
                                type="hidden"
                                name="roles[]"
                                value={roleName}
                            />
                        ))}
                    </div>

                    <div className="flex justify-end">
                        <Button type="submit">Update User</Button>
                    </div>
                </>
            )}
        </Form>
    );
}

export function AdminUsersContainer({
    children,
}: {
    children: React.ReactNode;
}) {
    return <div className="space-y-6">{children}</div>;
}

interface Role {
    id: number;
    name: string;
}

export function AdminUsersHeader() {
    const { props } = usePage<{ roles: Role[] }>();
    const [modalOpen, setModalOpen] = React.useState(false);

    return (
        <div className="mb-6">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold">Users</h1>
                    <p className="text-muted-foreground">
                        Manage system users and their roles
                    </p>
                </div>
                <Dialog open={modalOpen} onOpenChange={setModalOpen}>
                    <DialogTrigger asChild>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add User
                        </Button>
                    </DialogTrigger>
                    <DialogContent className="max-w-2xl">
                        <DialogHeader>
                            <DialogTitle>Create New User</DialogTitle>
                        </DialogHeader>
                        <UserForm
                            roles={props.roles}
                            onSuccess={() => setModalOpen(false)}
                        />
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    );
}
