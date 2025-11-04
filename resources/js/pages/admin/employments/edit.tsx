import { AdminLayout } from '@/components/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { EmploymentEditForm } from './components';

interface Client {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
}

interface Employment {
    id: number;
    position: string;
    hire_date: string;
    status: string;
    salary: string | null;
    work_location: string | null;
    effective_date: string;
    end_date: string | null;
    created_at: string;
    user: {
        id: number;
        name: string;
        email: string;
    };
    client: {
        id: number;
        name: string;
    } | null;
}

interface Props {
    employment: Employment;
    clients: Client[];
    users: User[];
}

export default function AdminEmploymentsEdit({
    employment,
    clients,
    users,
}: Props) {
    return (
        <AdminLayout currentPath="admin/employments">
            <Head title="Edit Employment" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            Edit Employment Record
                        </h1>
                        <p className="text-muted-foreground">
                            Update employment record details.
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
                        <EmploymentEditForm
                            employment={employment}
                            clients={clients}
                            users={users}
                        />
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
