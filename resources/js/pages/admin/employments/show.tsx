import { AdminLayout } from '@/components/layouts/admin-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head, Link, router } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowLeft,
    Building,
    Calendar,
    DollarSign,
    Edit,
    MapPin,
    User,
} from 'lucide-react';

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
}

export default function AdminEmploymentsShow({ employment }: Props) {
    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    const formatCurrency = (amount: string) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        }).format(parseFloat(amount));
    };

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'active':
                return 'bg-green-100 text-green-800';
            case 'inactive':
                return 'bg-yellow-100 text-yellow-800';
            case 'terminated':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    return (
        <AdminLayout currentPath="admin/employments">
            <Head title="Employment Details" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            Employment Details
                        </h1>
                        <p className="text-muted-foreground">
                            View employment record information.
                        </p>
                    </div>
                    <div className="flex gap-2">
                        {employment.status === 'active' && (
                            <Button
                                variant="destructive"
                                onClick={() => {
                                    if (
                                        confirm(
                                            'Are you sure you want to end this employment? This action cannot be undone.',
                                        )
                                    ) {
                                        router.patch(
                                            `/admin/employments/${employment.id}/end`,
                                        );
                                    }
                                }}
                            >
                                <AlertTriangle className="mr-2 h-4 w-4" />
                                End Employment
                            </Button>
                        )}
                        {employment.status !== 'active' && (
                            <Link
                                href={`/admin/employments/${employment.id}/edit`}
                            >
                                <Button>
                                    <Edit className="mr-2 h-4 w-4" />
                                    Edit Employment
                                </Button>
                            </Link>
                        )}
                        <Link href="/admin/employments">
                            <Button variant="outline">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Employments
                            </Button>
                        </Link>
                    </div>
                </div>

                <div className="grid gap-6 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <User className="h-5 w-5" />
                                Employee Information
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Employee Name
                                </label>
                                <p className="text-lg font-medium">
                                    {employment.user.name}
                                </p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Email
                                </label>
                                <p>{employment.user.email}</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Position
                                </label>
                                <p className="text-lg font-medium">
                                    {employment.position}
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Building className="h-5 w-5" />
                                Employment Details
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {employment.client && (
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">
                                        Client
                                    </label>
                                    <p>{employment.client.name}</p>
                                </div>
                            )}
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Status
                                </label>
                                <div className="mt-1">
                                    <Badge
                                        className={getStatusColor(
                                            employment.status,
                                        )}
                                    >
                                        {employment.status
                                            .charAt(0)
                                            .toUpperCase() +
                                            employment.status.slice(1)}
                                    </Badge>
                                </div>
                            </div>
                            {employment.work_location && (
                                <div>
                                    <label className="flex items-center gap-1 text-sm font-medium text-muted-foreground">
                                        <MapPin className="h-4 w-4" />
                                        Work Location
                                    </label>
                                    <p>{employment.work_location}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Calendar className="h-5 w-5" />
                                Important Dates
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Hire Date
                                </label>
                                <p>{formatDate(employment.hire_date)}</p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Effective Date
                                </label>
                                <p>{formatDate(employment.effective_date)}</p>
                            </div>
                            {employment.end_date && (
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">
                                        End Date
                                    </label>
                                    <p>{formatDate(employment.end_date)}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {employment.salary && (
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <DollarSign className="h-5 w-5" />
                                    Compensation
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">
                                        Salary
                                    </label>
                                    <p className="text-2xl font-bold text-green-600">
                                        {formatCurrency(employment.salary)}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Record Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="text-sm text-muted-foreground">
                            Created on {formatDate(employment.created_at)}
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
