import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useFormDefaults } from '@/hooks/use-form-defaults';
import { Form } from '@inertiajs/react';

interface ClientFormProps {
    onSuccess?: () => void;
}

export function ClientForm({ onSuccess }: ClientFormProps) {
    const formDefaults = useFormDefaults({
        name: '',
        email: '',
        phone: '',
        address: '',
        status: '',
        industry: '',
        contact_person: '',
        website: '',
    });

    return (
        <Form
            method="post"
            action="/admin/clients"
            onSuccess={() => {
                onSuccess?.();
            }}
            className="space-y-6"
        >
            <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="name">Company Name *</Label>
                    <Input
                        id="name"
                        name="name"
                        placeholder="Enter company name"
                        defaultValue={formDefaults.name}
                        required
                    />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="email">Email *</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        placeholder="Enter email address"
                        defaultValue={formDefaults.email}
                        required
                    />
                </div>
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="phone">Phone</Label>
                    <Input
                        id="phone"
                        name="phone"
                        type="tel"
                        placeholder="Enter phone number"
                        defaultValue={formDefaults.phone}
                    />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="status">Status *</Label>
                    <Select
                        name="status"
                        required
                        defaultValue={formDefaults.status}
                    >
                        <SelectTrigger>
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="prospect">Prospect</SelectItem>
                            <SelectItem value="active">Active</SelectItem>
                            <SelectItem value="inactive">Inactive</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <div className="space-y-2">
                <Label htmlFor="address">Address</Label>
                <Textarea
                    id="address"
                    name="address"
                    placeholder="Enter full address"
                    rows={3}
                    defaultValue={formDefaults.address}
                />
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="industry">Industry</Label>
                    <Input
                        id="industry"
                        name="industry"
                        placeholder="e.g., Technology, Healthcare"
                        defaultValue={formDefaults.industry}
                    />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="contact_person">Contact Person</Label>
                    <Input
                        id="contact_person"
                        name="contact_person"
                        placeholder="Enter contact person name"
                        defaultValue={formDefaults.contact_person}
                    />
                </div>
            </div>

            <div className="space-y-2">
                <Label htmlFor="website">Website</Label>
                <Input
                    id="website"
                    name="website"
                    type="url"
                    placeholder="https://example.com"
                    defaultValue={formDefaults.website}
                />
            </div>

            <div className="flex justify-end">
                <Button type="submit">Create Client</Button>
            </div>
        </Form>
    );
}

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

interface ClientEditFormProps {
    client: Client;
    onSuccess?: () => void;
}

export function ClientEditForm({ client, onSuccess }: ClientEditFormProps) {
    const formDefaults = useFormDefaults({
        name: client.name,
        email: client.email,
        phone: client.phone || '',
        address: client.address || '',
        status: client.status,
        industry: client.industry || '',
        contact_person: client.contact_person || '',
        website: client.website || '',
    });

    return (
        <Form
            method="put"
            action={`/admin/clients/${client.id}`}
            onSuccess={() => {
                onSuccess?.();
            }}
            className="space-y-6"
        >
            <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="name">Company Name *</Label>
                    <Input
                        id="name"
                        name="name"
                        placeholder="Enter company name"
                        defaultValue={formDefaults.name}
                        required
                    />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="email">Email *</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        placeholder="Enter email address"
                        defaultValue={formDefaults.email}
                        required
                    />
                </div>
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="phone">Phone</Label>
                    <Input
                        id="phone"
                        name="phone"
                        type="tel"
                        placeholder="Enter phone number"
                        defaultValue={formDefaults.phone}
                    />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="status">Status *</Label>
                    <Select
                        name="status"
                        defaultValue={formDefaults.status}
                        required
                    >
                        <SelectTrigger>
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="prospect">Prospect</SelectItem>
                            <SelectItem value="active">Active</SelectItem>
                            <SelectItem value="inactive">Inactive</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <div className="space-y-2">
                <Label htmlFor="address">Address</Label>
                <Textarea
                    id="address"
                    name="address"
                    placeholder="Enter full address"
                    rows={3}
                    defaultValue={formDefaults.address}
                />
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="industry">Industry</Label>
                    <Input
                        id="industry"
                        name="industry"
                        placeholder="e.g., Technology, Healthcare"
                        defaultValue={formDefaults.industry}
                    />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="contact_person">Contact Person</Label>
                    <Input
                        id="contact_person"
                        name="contact_person"
                        placeholder="Enter contact person name"
                        defaultValue={formDefaults.contact_person}
                    />
                </div>
            </div>

            <div className="space-y-2">
                <Label htmlFor="website">Website</Label>
                <Input
                    id="website"
                    name="website"
                    type="url"
                    placeholder="https://example.com"
                    defaultValue={formDefaults.website}
                />
            </div>

            <div className="flex justify-end">
                <Button type="submit">Update Client</Button>
            </div>
        </Form>
    );
}
