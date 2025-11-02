import { UserEditForm } from '@/components/admin/user-form';
import { AdminLayout } from '@/components/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

interface Role {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    roles: Array<{
        id: number;
        name: string;
    }>;
}

interface Props {
    user: User;
    roles: Role[];
}

export default function EditUser({ user, roles }: Props) {
    return (
        <AdminLayout currentPath="admin/users">
            <Head title={`Edit User - ${user.name}`} />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            Edit User
                        </h1>
                        <p className="text-muted-foreground">
                            Update user information and role assignments.
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
                        <UserEditForm user={user} roles={roles} />
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
