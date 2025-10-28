import { UserForm } from '@/components/admin/UserForm';
import { AdminLayout } from '@/components/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

interface Role {
    id: number;
    name: string;
}

interface Props {
    roles: Role[];
}

export default function CreateUser({ roles }: Props) {
    return (
        <AdminLayout currentPath="admin/users">
            <Head title="Create User" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            Create New User
                        </h1>
                        <p className="text-muted-foreground">
                            Add a new user to the system with appropriate roles.
                        </p>
                    </div>
                    <Link href="/admin/users">
                        <Button variant="outline">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Users
                        </Button>
                    </Link>
                </div>

                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>User Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <UserForm roles={roles} />
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
