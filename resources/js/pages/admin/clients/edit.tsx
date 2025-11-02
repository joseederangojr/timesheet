import { ClientEditForm } from '@/components/admin/client-form';
import { AdminLayout } from '@/components/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

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

export default function EditClient({ client }: Props) {
    return (
        <AdminLayout currentPath="admin/clients">
            <Head title={`Edit Client - ${client.name}`} />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            Edit Client
                        </h1>
                        <p className="text-muted-foreground">
                            Update client company information.
                        </p>
                    </div>
                    <Link href="/admin/clients">
                        <Button variant="outline">
                            <ArrowLeft className="mr-2 h-4 w-4" />
                            Back to Clients
                        </Button>
                    </Link>
                </div>

                <Card className="max-w-2xl">
                    <CardHeader>
                        <CardTitle>Client Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <ClientEditForm client={client} />
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
