import { AdminLayout } from '@/components/layouts/admin-layout';
import { CardGrid, StatsCard } from '@/components/ui/dashboard-cards';
import { FlashStatusMessage } from '@/components/ui/flash-status-message';

interface AdminDashboardProps {
    auth: {
        user: {
            name: string;
            email: string;
        };
    };
}

export default function AdminDashboard({ auth }: AdminDashboardProps) {
    return (
        <AdminLayout currentPath="/admin/dashboard">
            <div className="mb-6">
                <FlashStatusMessage className="mb-4" />
                <h1 className="text-3xl font-bold">Hello, {auth.user.name}</h1>
                <p className="text-muted-foreground">
                    Welcome to the admin dashboard
                </p>
            </div>

            <CardGrid>
                <StatsCard
                    title="Total Users"
                    value="--"
                    description="User management coming soon"
                />

                <StatsCard
                    title="System Status"
                    value="Online"
                    description="All systems operational"
                    valueClassName="text-green-600"
                />

                <StatsCard
                    title="Quick Actions"
                    value=""
                    description="Admin tools coming soon"
                />
            </CardGrid>
        </AdminLayout>
    );
}
