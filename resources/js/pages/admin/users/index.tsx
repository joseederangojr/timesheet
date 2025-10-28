import { AdminLayout } from '@/components/layouts/admin-layout';
import { AdminUsersDataTable, AdminUsersHeader } from './components';

export default function AdminUsersIndex() {
    return (
        <AdminLayout currentPath="admin/users">
            <div className="space-y-6">
                <AdminUsersHeader />
                <AdminUsersDataTable />
            </div>
        </AdminLayout>
    );
}
