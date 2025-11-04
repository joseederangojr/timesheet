import { AdminLayout } from '@/components/layouts/admin-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Briefcase, Edit, Plus } from 'lucide-react';

interface Role {
    id: number;
    name: string;
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
    client: {
        id: number;
        name: string;
    } | null;
}

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    roles: Role[];
    employments?: Employment[];
}

interface Props {
    user: User;
}

export default function ShowUser({ user }: Props) {
    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const formatDateShort = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    const isEmployee = user.roles.some((role) => role.name === 'employee');
    const activeEmployment = user.employments?.find(
        (emp) => emp.status === 'active',
    );
    const hasActiveEmployment = !!activeEmployment;

    return (
        <AdminLayout currentPath="admin/users">
            <Head title={`User Details - ${user.name}`} />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            User Details
                        </h1>
                        <p className="text-muted-foreground">
                            View user information and roles.
                        </p>
                    </div>
                    <div className="flex items-center gap-2">
                        <Link href={`/admin/users/${user.id}/edit`}>
                            <Button variant="outline">
                                <Edit className="mr-2 h-4 w-4" />
                                Edit User
                            </Button>
                        </Link>
                        {isEmployee && !hasActiveEmployment && (
                            <Link
                                href={`/admin/employments/create?user_id=${user.id}`}
                            >
                                <Button>
                                    <Plus className="mr-2 h-4 w-4" />
                                    Add Employment
                                </Button>
                            </Link>
                        )}
                        <Link href="/admin/users">
                            <Button variant="outline">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Users
                            </Button>
                        </Link>
                    </div>
                </div>

                <div className="grid gap-6 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Basic Information</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Name
                                </label>
                                <p className="text-lg font-medium">
                                    {user.name}
                                </p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Email
                                </label>
                                <p className="text-lg font-medium">
                                    {user.email}
                                </p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Email Verified
                                </label>
                                <Badge
                                    variant={
                                        user.email_verified_at
                                            ? 'default'
                                            : 'secondary'
                                    }
                                >
                                    {user.email_verified_at
                                        ? 'Verified'
                                        : 'Unverified'}
                                </Badge>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Joined
                                </label>
                                <p className="text-sm text-muted-foreground">
                                    {formatDate(user.created_at)}
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Roles & Permissions</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {user.roles.length > 0 ? (
                                <div className="flex flex-wrap gap-2">
                                    {user.roles.map((role) => (
                                        <Badge
                                            key={role.id}
                                            variant="secondary"
                                        >
                                            {role.name}
                                        </Badge>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-muted-foreground">
                                    No roles assigned
                                </p>
                            )}
                        </CardContent>
                    </Card>

                    {isEmployee && (
                        <Card className="md:col-span-2">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Briefcase className="h-5 w-5" />
                                    Employment Information
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {user.employments &&
                                user.employments.length > 0 ? (
                                    <div className="space-y-4">
                                        {user.employments.map((employment) => (
                                            <div
                                                key={employment.id}
                                                className="flex items-center justify-between rounded-lg border p-4"
                                            >
                                                <div className="space-y-1">
                                                    <div className="flex items-center gap-2">
                                                        <h4 className="font-medium">
                                                            {
                                                                employment.position
                                                            }
                                                        </h4>
                                                        <Badge
                                                            variant={
                                                                employment.status ===
                                                                'active'
                                                                    ? 'default'
                                                                    : 'secondary'
                                                            }
                                                        >
                                                            {employment.status}
                                                        </Badge>
                                                    </div>
                                                    {employment.client && (
                                                        <p className="text-sm text-muted-foreground">
                                                            {
                                                                employment
                                                                    .client.name
                                                            }
                                                        </p>
                                                    )}
                                                    <div className="flex items-center gap-4 text-sm text-muted-foreground">
                                                        <span>
                                                            Hired:{' '}
                                                            {formatDateShort(
                                                                employment.hire_date,
                                                            )}
                                                        </span>
                                                        {employment.end_date && (
                                                            <span>
                                                                Ended:{' '}
                                                                {formatDateShort(
                                                                    employment.end_date,
                                                                )}
                                                            </span>
                                                        )}
                                                        {employment.work_location && (
                                                            <span>
                                                                Location:{' '}
                                                                {
                                                                    employment.work_location
                                                                }
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                                <Link
                                                    href={`/admin/employments/${employment.id}`}
                                                >
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                    >
                                                        View Details
                                                    </Button>
                                                </Link>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="py-8 text-center">
                                        <Briefcase className="mx-auto h-12 w-12 text-muted-foreground" />
                                        <h3 className="mt-2 text-sm font-medium">
                                            No employment records
                                        </h3>
                                        <p className="mt-1 text-sm text-muted-foreground">
                                            This employee doesn't have any
                                            employment records yet.
                                        </p>
                                        {!hasActiveEmployment && (
                                            <div className="mt-4">
                                                <Link
                                                    href={`/admin/employments/create?user_id=${user.id}`}
                                                >
                                                    <Button>
                                                        <Plus className="mr-2 h-4 w-4" />
                                                        Add Employment Record
                                                    </Button>
                                                </Link>
                                            </div>
                                        )}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    )}
                </div>
            </div>
        </AdminLayout>
    );
}
