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
import { Form } from '@inertiajs/react';

interface ClientFormProps {
    onSuccess?: () => void;
}

export function ClientForm({ onSuccess }: ClientFormProps) {
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
                    />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="status">Status *</Label>
                    <Select name="status" required>
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
                />
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="industry">Industry</Label>
                    <Input
                        id="industry"
                        name="industry"
                        placeholder="e.g., Technology, Healthcare"
                    />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="contact_person">Contact Person</Label>
                    <Input
                        id="contact_person"
                        name="contact_person"
                        placeholder="Enter contact person name"
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
                        defaultValue={client.name}
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
                        defaultValue={client.email}
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
                        defaultValue={client.phone || ''}
                    />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="status">Status *</Label>
                    <Select name="status" defaultValue={client.status} required>
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
                    defaultValue={client.address || ''}
                />
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="industry">Industry</Label>
                    <Input
                        id="industry"
                        name="industry"
                        placeholder="e.g., Technology, Healthcare"
                        defaultValue={client.industry || ''}
                    />
                </div>

                <div className="space-y-2">
                    <Label htmlFor="contact_person">Contact Person</Label>
                    <Input
                        id="contact_person"
                        name="contact_person"
                        placeholder="Enter contact person name"
                        defaultValue={client.contact_person || ''}
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
                    defaultValue={client.website || ''}
                />
            </div>

            <div className="flex justify-end">
                <Button type="submit">Update Client</Button>
            </div>
        </Form>
    );
}
