import { ClientForm } from '@/components/admin/client-form';
import { AdminLayout } from '@/components/layouts/admin-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

export default function CreateClient() {
    return (
        <AdminLayout currentPath="admin/clients">
            <Head title="Create Client" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            Create New Client
                        </h1>
                        <p className="text-muted-foreground">
                            Add a new client company to the system.
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
                        <ClientForm />
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    );
}
