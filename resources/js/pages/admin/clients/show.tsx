import { AdminLayout } from '@/components/layouts/admin-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Edit, ExternalLink } from 'lucide-react';

interface Client {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    address: string | null;
    status: 'active' | 'inactive' | 'prospect';
    industry: string | null;
    contact_person: string | null;
    website: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    client: Client;
}

export default function ShowClient({ client }: Props) {
    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const getStatusBadgeVariant = (status: string) => {
        switch (status) {
            case 'active':
                return 'default';
            case 'inactive':
                return 'secondary';
            case 'prospect':
                return 'outline';
            default:
                return 'secondary';
        }
    };

    return (
        <AdminLayout currentPath="admin/clients">
            <Head title={`Client Details - ${client.name}`} />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            Client Details
                        </h1>
                        <p className="text-muted-foreground">
                            View client company information.
                        </p>
                    </div>
                    <div className="flex items-center gap-2">
                        <Link href={`/admin/clients/${client.id}/edit`}>
                            <Button variant="outline">
                                <Edit className="mr-2 h-4 w-4" />
                                Edit Client
                            </Button>
                        </Link>
                        <Link href="/admin/clients">
                            <Button variant="outline">
                                <ArrowLeft className="mr-2 h-4 w-4" />
                                Back to Clients
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
                                    Company Name
                                </label>
                                <p className="text-lg font-medium">
                                    {client.name}
                                </p>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Email
                                </label>
                                <p className="text-lg font-medium">
                                    <a
                                        href={`mailto:${client.email}`}
                                        className="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    >
                                        {client.email}
                                    </a>
                                </p>
                            </div>
                            {client.phone && (
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">
                                        Phone
                                    </label>
                                    <p className="text-lg font-medium">
                                        <a
                                            href={`tel:${client.phone}`}
                                            className="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            {client.phone}
                                        </a>
                                    </p>
                                </div>
                            )}
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Status
                                </label>
                                <div className="mt-1">
                                    <Badge
                                        variant={getStatusBadgeVariant(
                                            client.status,
                                        )}
                                    >
                                        {client.status.charAt(0).toUpperCase() +
                                            client.status.slice(1)}
                                    </Badge>
                                </div>
                            </div>
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Created
                                </label>
                                <p className="text-sm text-muted-foreground">
                                    {formatDate(client.created_at)}
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Additional Information</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            {client.contact_person && (
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">
                                        Contact Person
                                    </label>
                                    <p className="text-lg font-medium">
                                        {client.contact_person}
                                    </p>
                                </div>
                            )}
                            {client.industry && (
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">
                                        Industry
                                    </label>
                                    <p className="text-lg font-medium">
                                        {client.industry}
                                    </p>
                                </div>
                            )}
                            {client.website && (
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">
                                        Website
                                    </label>
                                    <p className="text-lg font-medium">
                                        <a
                                            href={client.website}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            {client.website}
                                            <ExternalLink className="h-3 w-3" />
                                        </a>
                                    </p>
                                </div>
                            )}
                            {client.address && (
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">
                                        Address
                                    </label>
                                    <p className="text-lg font-medium whitespace-pre-line">
                                        {client.address}
                                    </p>
                                </div>
                            )}
                            <div>
                                <label className="text-sm font-medium text-muted-foreground">
                                    Last Updated
                                </label>
                                <p className="text-sm text-muted-foreground">
                                    {formatDate(client.updated_at)}
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AdminLayout>
    );
}
