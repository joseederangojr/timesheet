import {
    UserSearch,
    UserTable,
    useUserSearch,
    useUserSort,
} from '@/components/features/user-management';
import { AdminLayout } from '@/components/layouts/admin-layout';
import { AuthProvider } from '@/contexts/auth-context';
import { SidebarProvider } from '@/contexts/sidebar-context';

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
        sort_by?: string;
        sort_direction?: 'asc' | 'desc';
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
    const { searchTerm, setSearchTerm, clearSearch } = useUserSearch({
        initialSearch: filters.search,
    });

    const { sortBy, sortDirection, handleSort } = useUserSort({
        initialSortBy: filters.sort_by,
        initialSortDirection: filters.sort_direction,
        currentSearch: searchTerm,
    });

    return (
        <AuthProvider auth={auth}>
            <SidebarProvider>
                <AdminLayout currentPath="/admin/users">
                    <div className="mb-6">
                        <h1 className="text-3xl font-bold">Users</h1>
                        <p className="text-muted-foreground">
                            Manage system users and their roles
                        </p>
                    </div>

                    <UserSearch
                        searchTerm={searchTerm}
                        onSearchChange={setSearchTerm}
                        onClearSearch={clearSearch}
                    />

                    <UserTable
                        users={users}
                        hasSearchFilter={!!filters.search}
                        sortBy={sortBy}
                        sortDirection={sortDirection}
                        onSort={handleSort}
                    />
                </AdminLayout>
            </SidebarProvider>
        </AuthProvider>
    );
}
