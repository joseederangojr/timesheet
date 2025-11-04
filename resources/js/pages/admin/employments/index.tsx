import { AdminLayout } from '@/components/layouts/admin-layout';
import { FlashStatusMessage } from '@/components/ui/flash-status-message';
import { Head } from '@inertiajs/react';
import {
    AdminEmploymentsDataTable,
    AdminEmploymentsHeader,
} from './components';

export default function AdminEmploymentsIndex() {
    return (
        <AdminLayout currentPath="admin/employments">
            <Head title="Employments" />

            <div className="space-y-6">
                <FlashStatusMessage />
                <AdminEmploymentsHeader />
                <AdminEmploymentsDataTable />
            </div>
        </AdminLayout>
    );
}
