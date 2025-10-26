import { AppLayout } from '@/components/layouts/app-layout';
import { CardGrid, DashboardCard } from '@/components/ui/dashboard-cards';
import { AuthProvider } from '@/contexts/auth-context';

interface DashboardProps {
    greeting?: string;
    auth: {
        user: {
            name: string;
            email: string;
        };
    };
}

export default function Dashboard({ greeting, auth }: DashboardProps) {
    return (
        <AuthProvider auth={auth}>
            <AppLayout>
                <div className="mb-6">
                    {greeting && (
                        <h1 className="text-3xl font-bold text-foreground">
                            {greeting}
                        </h1>
                    )}
                    <p className="text-muted-foreground">
                        Welcome to your dashboard
                    </p>
                </div>

                <CardGrid>
                    <DashboardCard
                        title="Profile"
                        description="Your account information"
                    >
                        <div className="space-y-2">
                            <p>
                                <span className="font-medium">Name:</span>{' '}
                                {auth.user.name}
                            </p>
                            <p>
                                <span className="font-medium">Email:</span>{' '}
                                {auth.user.email}
                            </p>
                        </div>
                    </DashboardCard>

                    <DashboardCard
                        title="Timesheet"
                        description="Track your time"
                    >
                        <p className="text-muted-foreground">
                            Your timesheet functionality will be here.
                        </p>
                    </DashboardCard>

                    <DashboardCard
                        title="Reports"
                        description="View your reports"
                    >
                        <p className="text-muted-foreground">
                            Your reports will be displayed here.
                        </p>
                    </DashboardCard>
                </CardGrid>
            </AppLayout>
        </AuthProvider>
    );
}
