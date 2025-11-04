import { AdminLayout } from '@/components/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { EmploymentForm } from './components';

interface Client {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface Props {
    clients: Client[];
    users: User[];
}

export default function AdminEmploymentsCreate({ clients, users }: Props) {
    return (
        <AdminLayout currentPath="admin/employments">
            <Head title="Create Employment" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            Create Employment Record
                        </h1>
                        <p className="text-muted-foreground">
                            Add a new employment record for an employee.
                        </p>
                    </div>
                    <Link href="/admin/employments">
                        <Button variant="outline">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Employments
                        </Button>
                    </Link>
                </div>

                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Employment Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <EmploymentForm clients={clients} users={users} />
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
