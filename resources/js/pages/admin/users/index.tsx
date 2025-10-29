import { AdminLayout } from '@/components/layouts/admin-layout';
import { FlashStatusMessage } from '@/components/ui/flash-status-message';
import { AdminUsersDataTable, AdminUsersHeader } from './components';

export default function AdminUsersIndex() {
    return (
        <AdminLayout currentPath="admin/users">
            <div className="space-y-6">
                <FlashStatusMessage />
                <AdminUsersHeader />
                <AdminUsersDataTable />
            </div>
        </AdminLayout>
    );
}
