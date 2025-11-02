import { ClientForm } from '@/components/admin/client-form';
import { ComboboxFilter } from '@/components/combobox-filter';
import { DataTableColumnHeader } from '@/components/data-table-column-header';
import { DataTablePagination } from '@/components/data-table-pagination';
import { DataTableViewOptions } from '@/components/data-table-view-options';
import { SearchInput } from '@/components/search-input';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
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
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

import { Paginated } from '@/types';
import { Link, router, usePage } from '@inertiajs/react';
import {
    ColumnDef,
    PaginationState,
    SortingState,
} from '@tanstack/react-table';
import { Edit, Plus } from 'lucide-react';
import * as React from 'react';

interface Client {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    address: string | null;
    status: 'active' | 'inactive' | 'prospect';
    industry: string | null;
    contact_person: string | null;
    website: string | null;
    created_at: string;
    updated_at: string;
}

interface ClientsFilters {
    search?: string;
    sort_by?: string;
    sort_direction?: string;
    status?: string;
    page?: string;
    per_page?: string;
}

export function ClientSearchInput() {
    const { props } = usePage<{ filters: ClientsFilters }>();
    const handleValueChange = (value?: string) =>
        onAdminClientsFilterChange({
            ...props.filters,
            search: value,
        });
    return (
        <SearchInput
            value={props.filters.search ?? ''}
            placeholder="Search by name, email, or contact person"
            onValueChange={handleValueChange}
        />
    );
}

export function onAdminClientsFilterChange(filters: ClientsFilters) {
    router.get(
        '/admin/clients',
        { ...filters },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

export function ClientStatusFilter() {
    const {
        props: { filters },
    } = usePage<{ filters: ClientsFilters }>();
    const value = filters?.status ?? '';

    const handleValueChange = (value?: string) => {
        onAdminClientsFilterChange({ ...filters, status: value });
    };

    const statusOptions = [
        { value: 'active', label: 'Active' },
        { value: 'inactive', label: 'Inactive' },
        { value: 'prospect', label: 'Prospect' },
    ];

    return (
        <ComboboxFilter
            options={statusOptions}
            value={value}
            onValueChange={handleValueChange}
            placeholder="Status"
        />
    );
}

const clientColumns: ColumnDef<Client>[] = [
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return (
                <DataTableColumnHeader column={column} title="Company Name" />
            );
        },
        cell: ({ row }) => {
            const client = row.original;
            return <div className="font-medium">{client.name}</div>;
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
        accessorKey: 'contact_person',
        header: 'Contact Person',
        cell: ({ row }) => (
            <div className="text-muted-foreground">
                {row.getValue('contact_person') || '—'}
            </div>
        ),
    },
    {
        accessorKey: 'industry',
        header: 'Industry',
        cell: ({ row }) => (
            <div className="text-muted-foreground">
                {row.getValue('industry') || '—'}
            </div>
        ),
    },
    {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) => (
            <ClientStatusBadge status={row.getValue('status')} />
        ),
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) => {
            return <DataTableColumnHeader column={column} title="Created" />;
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
        cell: ({ row }) => <ClientActionsCell client={row.original} />,
    },
];

export function AdminClientsDataTable() {
    'use no memo';
    const {
        props: { clients, filters },
    } = usePage<{ filters: ClientsFilters; clients: Paginated<Client> }>();

    const handleSortingChange = (sorting?: SortingState) => {
        if (!sorting?.length) return;
        const [sort] = sorting;
        onAdminClientsFilterChange({
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
        onAdminClientsFilterChange({
            ...filters,
            page: page.toString(),
            per_page: perPage.toString(),
        });
    };

    const table = useDataTable({
        columns: clientColumns,
        data: clients.data,
        pagination: {
            current_page: clients.current_page,
            last_page: clients.last_page,
            per_page: clients.per_page,
            total: clients.total,
            links: clients.links,
        },
        onPaginationChange: handlePaginationChange,
        sorting,
        onSortingChange: handleSortingChange,
    });

    return (
        <Card>
            <CardHeader>
                <CardTitle>Clients ({clients.total} total)</CardTitle>
            </CardHeader>
            <CardContent>
                <DataTableRoot>
                    <DataTableHeader>
                        <div className="flex flex-col gap-4 md:flex-row md:items-end">
                            <div className="flex-1">
                                <ClientSearchInput />
                            </div>
                            <ClientStatusFilter />
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

interface ClientStatusBadgeProps {
    status: string;
}

function ClientStatusBadge({ status }: ClientStatusBadgeProps) {
    const statusConfig = {
        active: {
            label: 'Active',
            className:
                'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300',
        },
        inactive: {
            label: 'Inactive',
            className:
                'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300',
        },
        prospect: {
            label: 'Prospect',
            className:
                'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300',
        },
    };

    const config =
        statusConfig[status as keyof typeof statusConfig] ||
        statusConfig.prospect;

    return <Badge className={config.className}>{config.label}</Badge>;
}

interface ClientActionsCellProps {
    client: Client;
}

function ClientActionsCell({ client }: ClientActionsCellProps) {
    return (
        <div className="flex items-center gap-2">
            <Link
                href={`/admin/clients/${client.id}`}
                data-testid={`view-client-${client.id}`}
            >
                <Button variant="ghost" size="sm">
                    View
                </Button>
            </Link>
            <ClientEditDialog client={client} />
        </div>
    );
}

interface ClientEditDialogProps {
    client: Client;
}

function ClientEditDialog({ client }: ClientEditDialogProps) {
    return (
        <Link
            href={`/admin/clients/${client.id}/edit`}
            data-testid={`edit-client-${client.id}`}
        >
            <Button variant="ghost" size="sm">
                <Edit className="h-4 w-4" />
            </Button>
        </Link>
    );
}

export function AdminClientsContainer({
    children,
}: {
    children: React.ReactNode;
}) {
    return <div className="space-y-6">{children}</div>;
}

export function AdminClientsHeader() {
    const [modalOpen, setModalOpen] = React.useState(false);

    return (
        <div className="mb-6">
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold">Clients</h1>
                    <p className="text-muted-foreground">
                        Manage client companies and their information
                    </p>
                </div>
                <Dialog open={modalOpen} onOpenChange={setModalOpen}>
                    <DialogTrigger asChild>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Client
                        </Button>
                    </DialogTrigger>
                    <DialogContent className="max-w-2xl">
                        <DialogHeader>
                            <DialogTitle>Create New Client</DialogTitle>
                        </DialogHeader>
                        <ClientForm onSuccess={() => setModalOpen(false)} />
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    );
}
