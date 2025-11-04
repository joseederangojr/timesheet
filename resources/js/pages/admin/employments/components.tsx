import { ComboboxFilter } from '@/components/combobox-filter';
import { DataTableColumnHeader } from '@/components/data-table-column-header';
import { DataTablePagination } from '@/components/data-table-pagination';
import { DataTableViewOptions } from '@/components/data-table-view-options';
import { SearchInput } from '@/components/search-input';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { ButtonGroup } from '@/components/ui/button-group';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    DataTable,
    DataTableContent,
    DataTableFooter,
    DataTableHeader,
    DataTableRoot,
    useDataTable,
} from '@/components/ui/data-table';

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Paginated } from '@/types';
import { Link, router, usePage } from '@inertiajs/react';
import { ColumnDef, SortingState } from '@tanstack/react-table';
import { ChevronDown, Edit, Eye, Plus, Trash2 } from 'lucide-react';
import * as React from 'react';

interface Employment {
    id: number;
    position: string;
    hire_date: string;
    status: string;
    salary: string | null;
    work_location: string | null;
    effective_date: string;
    end_date: string | null;
    created_at: string;
    user: {
        id: number;
        name: string;
        email: string;
    };
    client: {
        id: number;
        name: string;
    } | null;
}

interface EmploymentsFilters {
    search?: string;
    sort_by?: string;
    sort_direction?: string;
    status?: string;
    client?: string;
    page?: string;
    per_page?: string;
}

const employmentColumns: ColumnDef<Employment>[] = [
    {
        accessorKey: 'user.name',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Employee" />
        ),
        cell: ({ row }) => {
            const employment = row.original;
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
                            {getInitials(employment.user.name)}
                        </AvatarFallback>
                    </Avatar>
                    <div>
                        <div className="font-medium">
                            {employment.user.name}
                        </div>
                        <div className="text-sm text-muted-foreground">
                            {employment.user.email}
                        </div>
                    </div>
                </div>
            );
        },
    },
    {
        accessorKey: 'position',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Position" />
        ),
    },
    {
        accessorKey: 'client.name',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Client" />
        ),
        cell: ({ row }) => {
            const employment = row.original;
            return employment.client?.name || '—';
        },
    },
    {
        accessorKey: 'status',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Status" />
        ),
        cell: ({ row }) => {
            const employment = row.original;
            const statusColors = {
                active: 'bg-green-100 text-green-800',
                inactive: 'bg-yellow-100 text-yellow-800',
                terminated: 'bg-red-100 text-red-800',
            };

            return (
                <span
                    className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                        statusColors[
                            employment.status as keyof typeof statusColors
                        ] || 'bg-gray-100 text-gray-800'
                    }`}
                >
                    {employment.status.charAt(0).toUpperCase() +
                        employment.status.slice(1)}
                </span>
            );
        },
    },
    {
        accessorKey: 'work_location',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Location" />
        ),
        cell: ({ row }) => {
            const employment = row.original;
            return employment.work_location || '—';
        },
    },
    {
        accessorKey: 'hire_date',
        header: ({ column }) => (
            <DataTableColumnHeader column={column} title="Hire Date" />
        ),
        cell: ({ row }) => {
            const employment = row.original;
            const formatDate = (dateString: string) => {
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                });
            };
            return (
                <div className="text-muted-foreground">
                    {formatDate(employment.hire_date)}
                </div>
            );
        },
    },
    {
        id: 'actions',
        header: 'Actions',
        cell: ({ row }) => <EmploymentActionsCell employment={row.original} />,
    },
];

export function EmploymentSearchInput() {
    const { props } = usePage<{ filters: EmploymentsFilters }>();
    const handleValueChange = (value?: string) =>
        onAdminEmploymentsFilterChange({
            ...props.filters,
            search: value,
        });
    return (
        <SearchInput
            value={props.filters.search ?? ''}
            placeholder="Search by position or location"
            onValueChange={handleValueChange}
        />
    );
}

export function onAdminEmploymentsFilterChange(filters: EmploymentsFilters) {
    router.get(
        '/admin/employments',
        { ...filters },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

export function EmploymentStatusFilter() {
    const {
        props: { filters },
    } = usePage<{ filters: EmploymentsFilters }>();
    const value = filters?.status ?? '';

    const handleValueChange = (value?: string) => {
        onAdminEmploymentsFilterChange({ ...filters, status: value });
    };

    return (
        <ComboboxFilter
            options={[
                { value: '', label: 'All Statuses' },
                { value: 'active', label: 'Active' },
                { value: 'inactive', label: 'Inactive' },
                { value: 'terminated', label: 'Terminated' },
            ]}
            value={value}
            onValueChange={handleValueChange}
            placeholder="Filter by status"
        />
    );
}

export function EmploymentClientFilter() {
    const {
        props: { filters, clients },
    } = usePage<{
        filters: EmploymentsFilters;
        clients: Array<{ id: number; name: string }>;
    }>();
    const value = filters?.client ?? '';

    const handleValueChange = (value?: string) => {
        onAdminEmploymentsFilterChange({ ...filters, client: value });
    };

    return (
        <ComboboxFilter
            options={[
                { value: '', label: 'All Clients' },
                ...clients.map((client) => ({
                    value: client.name,
                    label: client.name,
                })),
            ]}
            value={value}
            onValueChange={handleValueChange}
            placeholder="Filter by client"
        />
    );
}

interface EmploymentActionsCellProps {
    employment: Employment;
}

function EmploymentActionsCell({ employment }: EmploymentActionsCellProps) {
    return (
        <ButtonGroup>
            <Button variant="outline" size="sm" asChild>
                <Link href={`/admin/employments/${employment.id}`}>
                    <Eye className="mr-2 h-4 w-4" />
                    View
                </Link>
            </Button>
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button
                        variant="outline"
                        size="sm"
                        aria-label="More actions"
                    >
                        <ChevronDown className="size-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" className="w-32">
                    <DropdownMenuItem asChild>
                        <Link href={`/admin/employments/${employment.id}/edit`}>
                            <Edit className="mr-2 h-4 w-4" />
                            Edit
                        </Link>
                    </DropdownMenuItem>
                    <DropdownMenuItem
                        className="text-destructive"
                        onClick={() => {
                            if (
                                confirm(
                                    'Are you sure you want to delete this employment record?',
                                )
                            ) {
                                router.delete(
                                    `/admin/employments/${employment.id}`,
                                );
                            }
                        }}
                    >
                        <Trash2 className="mr-2 h-4 w-4" />
                        Delete
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </ButtonGroup>
    );
}

export function AdminEmploymentsDataTable() {
    'use no memo';
    const {
        props: { employments, filters },
    } = usePage<{
        filters: EmploymentsFilters;
        employments: Paginated<Employment>;
    }>();

    const handleSortingChange = (sorting?: SortingState) => {
        if (!sorting?.length) return;
        const [sort] = sorting;
        onAdminEmploymentsFilterChange({
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

    const handlePaginationChange = (pagination?: {
        pageIndex: number;
        pageSize: number;
    }) => {
        if (!pagination) return;
        const page = (pagination?.pageIndex || 0) + 1;
        const perPage = pagination?.pageSize || 15;
        onAdminEmploymentsFilterChange({
            ...filters,
            page: page.toString(),
            per_page: perPage.toString(),
        });
    };

    const table = useDataTable({
        columns: employmentColumns,
        data: employments.data,
        pagination: {
            current_page: employments.current_page,
            last_page: employments.last_page,
            per_page: employments.per_page,
            total: employments.total,
            links: employments.links,
        },
        onPaginationChange: handlePaginationChange,
        sorting,
        onSortingChange: handleSortingChange,
    });

    return (
        <Card>
            <CardHeader>
                <CardTitle>
                    Employment Records ({employments.total} total)
                </CardTitle>
            </CardHeader>
            <CardContent>
                <DataTableRoot>
                    <DataTableHeader>
                        <div className="flex flex-col gap-4 md:flex-row md:items-end">
                            <div className="flex-1">
                                <EmploymentSearchInput />
                            </div>
                            <EmploymentStatusFilter />
                            <EmploymentClientFilter />
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

export function AdminEmploymentsHeader() {
    return (
        <div className="mb-6">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold">Employments</h1>
                    <p className="text-muted-foreground">
                        Manage employee employment records and assignments
                    </p>
                </div>
                <Button asChild>
                    <Link href="/admin/employments/create">
                        <Plus className="mr-2 h-4 w-4" />
                        Add Employment
                    </Link>
                </Button>
            </div>
        </div>
    );
}

import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useFormDefaults } from '@/hooks/use-form-defaults';
import { cn } from '@/lib/utils';
import { Form } from '@inertiajs/react';
import { Check, ChevronsUpDown } from 'lucide-react';
import { useState } from 'react';

interface Client {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface EmploymentFormProps {
    clients: Client[];
    users: User[];
    onSuccess?: () => void;
}

export function EmploymentForm({
    clients,
    users,
    onSuccess,
}: EmploymentFormProps) {
    const { url } = usePage();
    const urlParams = new URLSearchParams(url.split('?')[1]);
    const preselectedUserId = urlParams.get('user_id');

    const formDefaults = useFormDefaults({
        user_id: preselectedUserId || '',
        client_id: '',
        position: '',
        hire_date: '',
        status: 'active',
        salary: '',
        work_location: '',
        effective_date: '',
        end_date: '',
    });

    const [selectedUserId, setSelectedUserId] = useState<string>(
        formDefaults.user_id,
    );
    const [selectedClientId, setSelectedClientId] = useState<string>(
        formDefaults.client_id,
    );
    const [userOpen, setUserOpen] = useState(false);
    const [clientOpen, setClientOpen] = useState(false);

    const selectedUser = users.find(
        (user) => user.id.toString() === selectedUserId,
    );
    const selectedClient = clients.find(
        (client) => client.id.toString() === selectedClientId,
    );

    return (
        <Form
            method="post"
            action="/admin/employments"
            onSuccess={() => {
                setSelectedUserId('');
                setSelectedClientId('');
                onSuccess?.();
            }}
            className="space-y-6"
        >
            {({ errors }) => (
                <>
                    <div>
                        <Label>Employee</Label>
                        {preselectedUserId ? (
                            // Read-only display when user is pre-selected from URL
                            <div className="flex items-center space-x-2 rounded-md border border-input bg-muted px-3 py-2">
                                <div className="flex-1">
                                    <div className="font-medium">
                                        {selectedUser?.name}
                                    </div>
                                    <div className="text-sm text-muted-foreground">
                                        {selectedUser?.email}
                                    </div>
                                </div>
                                <div className="text-xs text-muted-foreground">
                                    Pre-selected from user page
                                </div>
                            </div>
                        ) : (
                            // Editable combobox when no pre-selected user
                            <Popover open={userOpen} onOpenChange={setUserOpen}>
                                <PopoverTrigger asChild>
                                    <Button
                                        variant="outline"
                                        role="combobox"
                                        aria-expanded={userOpen}
                                        className="w-full justify-between"
                                    >
                                        {selectedUser
                                            ? `${selectedUser.name} (${selectedUser.email})`
                                            : 'Select employee...'}
                                        <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent className="w-full p-0">
                                    <Command>
                                        <CommandInput placeholder="Search employees..." />
                                        <CommandList>
                                            <CommandEmpty>
                                                No employees found.
                                            </CommandEmpty>
                                            <CommandGroup>
                                                {users.map((user) => (
                                                    <CommandItem
                                                        key={user.id}
                                                        onSelect={() => {
                                                            setSelectedUserId(
                                                                user.id.toString(),
                                                            );
                                                            setUserOpen(false);
                                                        }}
                                                    >
                                                        <Check
                                                            className={cn(
                                                                'mr-2 h-4 w-4',
                                                                selectedUserId ===
                                                                    user.id.toString()
                                                                    ? 'opacity-100'
                                                                    : 'opacity-0',
                                                            )}
                                                        />
                                                        <div>
                                                            <div className="font-medium">
                                                                {user.name}
                                                            </div>
                                                            <div className="text-sm text-muted-foreground">
                                                                {user.email}
                                                            </div>
                                                        </div>
                                                    </CommandItem>
                                                ))}
                                            </CommandGroup>
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>
                        )}
                        {errors.user_id && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.user_id}
                            </p>
                        )}
                        <input
                            type="hidden"
                            name="user_id"
                            value={selectedUserId}
                        />
                    </div>

                    <div>
                        <Label>Client (Optional)</Label>
                        <Popover open={clientOpen} onOpenChange={setClientOpen}>
                            <PopoverTrigger asChild>
                                <Button
                                    variant="outline"
                                    role="combobox"
                                    aria-expanded={clientOpen}
                                    className="w-full justify-between"
                                >
                                    {selectedClient
                                        ? selectedClient.name
                                        : 'Select client...'}
                                    <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent className="w-full p-0">
                                <Command>
                                    <CommandInput placeholder="Search clients..." />
                                    <CommandList>
                                        <CommandEmpty>
                                            No clients found.
                                        </CommandEmpty>
                                        <CommandGroup>
                                            {clients.map((client) => (
                                                <CommandItem
                                                    key={client.id}
                                                    onSelect={() => {
                                                        setSelectedClientId(
                                                            client.id.toString(),
                                                        );
                                                        setClientOpen(false);
                                                    }}
                                                >
                                                    <Check
                                                        className={cn(
                                                            'mr-2 h-4 w-4',
                                                            selectedClientId ===
                                                                client.id.toString()
                                                                ? 'opacity-100'
                                                                : 'opacity-0',
                                                        )}
                                                    />
                                                    {client.name}
                                                </CommandItem>
                                            ))}
                                        </CommandGroup>
                                    </CommandList>
                                </Command>
                            </PopoverContent>
                        </Popover>
                        {errors.client_id && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.client_id}
                            </p>
                        )}
                        <input
                            type="hidden"
                            name="client_id"
                            value={selectedClientId}
                        />
                    </div>

                    <div>
                        <Label htmlFor="position">Position</Label>
                        <Input
                            id="position"
                            name="position"
                            placeholder="Enter position title"
                            defaultValue={formDefaults.position}
                            required
                        />
                        {errors.position && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.position}
                            </p>
                        )}
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <Label htmlFor="hire_date">Hire Date</Label>
                            <Input
                                id="hire_date"
                                name="hire_date"
                                type="date"
                                defaultValue={formDefaults.hire_date}
                                required
                            />
                            {errors.hire_date && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.hire_date}
                                </p>
                            )}
                        </div>

                        <div>
                            <Label htmlFor="effective_date">
                                Effective Date
                            </Label>
                            <Input
                                id="effective_date"
                                name="effective_date"
                                type="date"
                                defaultValue={formDefaults.effective_date}
                                required
                            />
                            {errors.effective_date && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.effective_date}
                                </p>
                            )}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <Label htmlFor="status">Status</Label>
                            <Select
                                name="status"
                                defaultValue={formDefaults.status}
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="active">
                                        Active
                                    </SelectItem>
                                    <SelectItem value="inactive">
                                        Inactive
                                    </SelectItem>
                                    <SelectItem value="terminated">
                                        Terminated
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            {errors.status && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.status}
                                </p>
                            )}
                        </div>

                        <div>
                            <Label htmlFor="salary">Salary (Optional)</Label>
                            <Input
                                id="salary"
                                name="salary"
                                placeholder="0.00"
                                defaultValue={formDefaults.salary}
                            />
                            {errors.salary && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.salary}
                                </p>
                            )}
                        </div>
                    </div>

                    <div>
                        <Label htmlFor="work_location">
                            Work Location (Optional)
                        </Label>
                        <Input
                            id="work_location"
                            name="work_location"
                            placeholder="Enter work location"
                            defaultValue={formDefaults.work_location}
                        />
                        {errors.work_location && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.work_location}
                            </p>
                        )}
                    </div>

                    <div>
                        <Label htmlFor="end_date">End Date (Optional)</Label>
                        <Input
                            id="end_date"
                            name="end_date"
                            type="date"
                            defaultValue={formDefaults.end_date}
                        />
                        {errors.end_date && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.end_date}
                            </p>
                        )}
                    </div>

                    <div className="flex justify-end">
                        <Button type="submit">Create Employment</Button>
                    </div>
                </>
            )}
        </Form>
    );
}

interface Employment {
    id: number;
    position: string;
    hire_date: string;
    status: string;
    salary: string | null;
    work_location: string | null;
    effective_date: string;
    end_date: string | null;
    created_at: string;
    user: {
        id: number;
        name: string;
        email: string;
    };
    client: {
        id: number;
        name: string;
    } | null;
}

interface EmploymentEditFormProps {
    employment: Employment;
    clients: Client[];
    users: User[];
    onSuccess?: () => void;
}

export function EmploymentEditForm({
    employment,
    clients,
    users,
    onSuccess,
}: EmploymentEditFormProps) {
    const formDefaults = useFormDefaults({
        user_id: employment.user.id.toString(),
        client_id: employment.client?.id.toString() || '',
        position: employment.position,
        hire_date: employment.hire_date.split('T')[0], // Format for date input
        status: employment.status,
        salary: employment.salary || '',
        work_location: employment.work_location || '',
        effective_date: employment.effective_date.split('T')[0], // Format for date input
        end_date: employment.end_date ? employment.end_date.split('T')[0] : '',
    });

    const [selectedUserId, setSelectedUserId] = useState<string>(
        formDefaults.user_id,
    );
    const [selectedClientId, setSelectedClientId] = useState<string>(
        formDefaults.client_id,
    );
    const [userOpen, setUserOpen] = useState(false);
    const [clientOpen, setClientOpen] = useState(false);

    const selectedUser = users.find(
        (user) => user.id.toString() === selectedUserId,
    );
    const selectedClient = clients.find(
        (client) => client.id.toString() === selectedClientId,
    );

    return (
        <Form
            method="put"
            action={`/admin/employments/${employment.id}`}
            onSuccess={onSuccess}
            className="space-y-6"
        >
            {({ errors }) => (
                <>
                    <div>
                        <Label>Employee</Label>
                        <Popover open={userOpen} onOpenChange={setUserOpen}>
                            <PopoverTrigger asChild>
                                <Button
                                    variant="outline"
                                    role="combobox"
                                    aria-expanded={userOpen}
                                    className="w-full justify-between"
                                >
                                    {selectedUser
                                        ? `${selectedUser.name} (${selectedUser.email})`
                                        : 'Select employee...'}
                                    <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent className="w-full p-0">
                                <Command>
                                    <CommandInput placeholder="Search employees..." />
                                    <CommandList>
                                        <CommandEmpty>
                                            No employees found.
                                        </CommandEmpty>
                                        <CommandGroup>
                                            {users.map((user) => (
                                                <CommandItem
                                                    key={user.id}
                                                    onSelect={() => {
                                                        setSelectedUserId(
                                                            user.id.toString(),
                                                        );
                                                        setUserOpen(false);
                                                    }}
                                                >
                                                    <Check
                                                        className={cn(
                                                            'mr-2 h-4 w-4',
                                                            selectedUserId ===
                                                                user.id.toString()
                                                                ? 'opacity-100'
                                                                : 'opacity-0',
                                                        )}
                                                    />
                                                    <div>
                                                        <div className="font-medium">
                                                            {user.name}
                                                        </div>
                                                        <div className="text-sm text-muted-foreground">
                                                            {user.email}
                                                        </div>
                                                    </div>
                                                </CommandItem>
                                            ))}
                                        </CommandGroup>
                                    </CommandList>
                                </Command>
                            </PopoverContent>
                        </Popover>
                        {errors.user_id && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.user_id}
                            </p>
                        )}
                        <input
                            type="hidden"
                            name="user_id"
                            value={selectedUserId}
                        />
                    </div>

                    <div>
                        <Label>Client (Optional)</Label>
                        <Popover open={clientOpen} onOpenChange={setClientOpen}>
                            <PopoverTrigger asChild>
                                <Button
                                    variant="outline"
                                    role="combobox"
                                    aria-expanded={clientOpen}
                                    className="w-full justify-between"
                                >
                                    {selectedClient
                                        ? selectedClient.name
                                        : 'Select client...'}
                                    <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent className="w-full p-0">
                                <Command>
                                    <CommandInput placeholder="Search clients..." />
                                    <CommandList>
                                        <CommandEmpty>
                                            No clients found.
                                        </CommandEmpty>
                                        <CommandGroup>
                                            {clients.map((client) => (
                                                <CommandItem
                                                    key={client.id}
                                                    onSelect={() => {
                                                        setSelectedClientId(
                                                            client.id.toString(),
                                                        );
                                                        setClientOpen(false);
                                                    }}
                                                >
                                                    <Check
                                                        className={cn(
                                                            'mr-2 h-4 w-4',
                                                            selectedClientId ===
                                                                client.id.toString()
                                                                ? 'opacity-100'
                                                                : 'opacity-0',
                                                        )}
                                                    />
                                                    {client.name}
                                                </CommandItem>
                                            ))}
                                        </CommandGroup>
                                    </CommandList>
                                </Command>
                            </PopoverContent>
                        </Popover>
                        {errors.client_id && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.client_id}
                            </p>
                        )}
                        <input
                            type="hidden"
                            name="client_id"
                            value={selectedClientId}
                        />
                    </div>

                    <div>
                        <Label htmlFor="position">Position</Label>
                        <Input
                            id="position"
                            name="position"
                            placeholder="Enter position title"
                            defaultValue={formDefaults.position}
                            required
                        />
                        {errors.position && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.position}
                            </p>
                        )}
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <Label htmlFor="hire_date">Hire Date</Label>
                            <Input
                                id="hire_date"
                                name="hire_date"
                                type="date"
                                defaultValue={formDefaults.hire_date}
                                required
                            />
                            {errors.hire_date && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.hire_date}
                                </p>
                            )}
                        </div>

                        <div>
                            <Label htmlFor="effective_date">
                                Effective Date
                            </Label>
                            <Input
                                id="effective_date"
                                name="effective_date"
                                type="date"
                                defaultValue={formDefaults.effective_date}
                                required
                            />
                            {errors.effective_date && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.effective_date}
                                </p>
                            )}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <Label htmlFor="status">Status</Label>
                            <Select
                                name="status"
                                defaultValue={formDefaults.status}
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="active">
                                        Active
                                    </SelectItem>
                                    <SelectItem value="inactive">
                                        Inactive
                                    </SelectItem>
                                    <SelectItem value="terminated">
                                        Terminated
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            {errors.status && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.status}
                                </p>
                            )}
                        </div>

                        <div>
                            <Label htmlFor="salary">Salary (Optional)</Label>
                            <Input
                                id="salary"
                                name="salary"
                                placeholder="0.00"
                                defaultValue={formDefaults.salary}
                            />
                            {errors.salary && (
                                <p className="mt-1 text-sm text-red-600">
                                    {errors.salary}
                                </p>
                            )}
                        </div>
                    </div>

                    <div>
                        <Label htmlFor="work_location">
                            Work Location (Optional)
                        </Label>
                        <Input
                            id="work_location"
                            name="work_location"
                            placeholder="Enter work location"
                            defaultValue={formDefaults.work_location}
                        />
                        {errors.work_location && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.work_location}
                            </p>
                        )}
                    </div>

                    <div>
                        <Label htmlFor="end_date">End Date (Optional)</Label>
                        <Input
                            id="end_date"
                            name="end_date"
                            type="date"
                            defaultValue={formDefaults.end_date}
                        />
                        {errors.end_date && (
                            <p className="mt-1 text-sm text-red-600">
                                {errors.end_date}
                            </p>
                        )}
                    </div>

                    <div className="flex justify-end">
                        <Button type="submit">Update Employment</Button>
                    </div>
                </>
            )}
        </Form>
    );
}
