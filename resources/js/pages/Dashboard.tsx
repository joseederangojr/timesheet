import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Form } from '@inertiajs/react';
import { LogOut } from 'lucide-react';

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
        <div className="min-h-screen bg-background p-4">
            <div className="mx-auto max-w-4xl">
                <div className="mb-6 flex items-center justify-between">
                    <div>
                        {greeting && (
                            <h1 className="text-3xl font-bold text-foreground">
                                {greeting}
                            </h1>
                        )}
                        <p className="text-muted-foreground">
                            Welcome to your dashboard
                        </p>
                    </div>
                    <Form action="/auth/session" method="delete">
                        {({ processing }) => (
                            <Button
                                type="submit"
                                variant="outline"
                                disabled={processing}
                                className="flex items-center gap-2"
                            >
                                <LogOut className="h-4 w-4" />
                                {processing ? 'Signing out...' : 'Sign Out'}
                            </Button>
                        )}
                    </Form>
                </div>

                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <Card>
                        <CardHeader>
                            <CardTitle>Profile</CardTitle>
                            <CardDescription>
                                Your account information
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
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
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Timesheet</CardTitle>
                            <CardDescription>Track your time</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p className="text-muted-foreground">
                                Your timesheet functionality will be here.
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Reports</CardTitle>
                            <CardDescription>View your reports</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p className="text-muted-foreground">
                                Your reports will be displayed here.
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    );
}
