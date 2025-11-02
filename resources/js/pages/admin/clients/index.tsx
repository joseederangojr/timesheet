import { AdminLayout } from '@/components/layouts/admin-layout';
import { FlashStatusMessage } from '@/components/ui/flash-status-message';
import { AdminClientsDataTable, AdminClientsHeader } from './components';

export default function AdminClientsIndex() {
    return (
        <AdminLayout currentPath="admin/clients">
            <div className="space-y-6">
                <FlashStatusMessage />
                <AdminClientsHeader />
                <AdminClientsDataTable />
            </div>
        </AdminLayout>
    );
}
