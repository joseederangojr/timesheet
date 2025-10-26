import { AdminLayout } from '@/components/layouts/admin-layout';
import { CardGrid, StatsCard } from '@/components/ui/dashboard-cards';
import { AuthProvider } from '@/contexts/auth-context';
import { SidebarProvider } from '@/contexts/sidebar-context';

interface AdminDashboardProps {
    greeting?: string;
    auth: {
        user: {
            name: string;
            email: string;
        };
    };
}

export default function AdminDashboard({
    greeting,
    auth,
}: AdminDashboardProps) {
    return (
        <AuthProvider auth={auth}>
            <SidebarProvider>
                <AdminLayout currentPath="/admin/dashboard">
                    <div className="mb-6">
                        <h1 className="text-3xl font-bold">
                            {greeting || `Hello, ${auth.user.name}`}
                        </h1>
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
            </SidebarProvider>
        </AuthProvider>
    );
}
