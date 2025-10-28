import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { Form } from '@inertiajs/react';
import { Check, ChevronsUpDown, X } from 'lucide-react';
import { useState } from 'react';

interface Role {
    id: number;
    name: string;
}

interface UserFormProps {
    roles: Role[];
    onSuccess?: () => void;
}

export function UserForm({ roles, onSuccess }: UserFormProps) {
    const [selectedRoles, setSelectedRoles] = useState<string[]>([]);
    const [open, setOpen] = useState(false);

    const toggleRole = (roleName: string) => {
        setSelectedRoles((prev) =>
            prev.includes(roleName)
                ? prev.filter((r) => r !== roleName)
                : [...prev, roleName],
        );
    };

    const removeRole = (roleName: string) => {
        setSelectedRoles((prev) => prev.filter((r) => r !== roleName));
    };

    return (
        <Form
            method="post"
            action="/admin/users"
            onSuccess={() => {
                setSelectedRoles([]);
                onSuccess?.();
            }}
            className="space-y-6"
        >
            <div>
                <Label htmlFor="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    placeholder="Enter user name"
                    required
                />
            </div>

            <div>
                <Label htmlFor="email">Email</Label>
                <Input
                    id="email"
                    name="email"
                    type="email"
                    placeholder="Enter email address"
                    required
                />
            </div>

            <div>
                <Label htmlFor="password">Password</Label>
                <Input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Enter password"
                    required
                />
            </div>

            <div>
                <Label>Roles</Label>
                <Popover open={open} onOpenChange={setOpen}>
                    <PopoverTrigger asChild>
                        <Button
                            variant="outline"
                            role="combobox"
                            aria-expanded={open}
                            className="w-full justify-between"
                        >
                            {selectedRoles.length > 0
                                ? `${selectedRoles.length} role(s) selected`
                                : 'Select roles...'}
                            <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent className="w-full p-0">
                        <Command>
                            <CommandInput placeholder="Search roles..." />
                            <CommandList>
                                <CommandEmpty>No roles found.</CommandEmpty>
                                <CommandGroup>
                                    {roles.map((role) => (
                                        <CommandItem
                                            key={role.id}
                                            onSelect={() =>
                                                toggleRole(role.name)
                                            }
                                        >
                                            <Check
                                                className={cn(
                                                    'mr-2 h-4 w-4',
                                                    selectedRoles.includes(
                                                        role.name,
                                                    )
                                                        ? 'opacity-100'
                                                        : 'opacity-0',
                                                )}
                                            />
                                            {role.name}
                                        </CommandItem>
                                    ))}
                                </CommandGroup>
                            </CommandList>
                        </Command>
                    </PopoverContent>
                </Popover>
                {selectedRoles.length > 0 && (
                    <div className="mt-2 flex flex-wrap gap-2">
                        {selectedRoles.map((roleName) => (
                            <Badge
                                key={roleName}
                                variant="secondary"
                                className="flex items-center gap-1"
                            >
                                {roleName}
                                <button
                                    type="button"
                                    onClick={() => removeRole(roleName)}
                                    className="ml-1 rounded-full p-0.5 hover:bg-secondary-foreground/20"
                                >
                                    <X className="h-3 w-3" />
                                </button>
                            </Badge>
                        ))}
                    </div>
                )}
                {/* Hidden inputs for selected roles */}
                {selectedRoles.map((roleName) => (
                    <input
                        key={roleName}
                        type="hidden"
                        name="roles[]"
                        value={roleName}
                    />
                ))}
            </div>

            <div className="flex justify-end">
                <Button type="submit">Create User</Button>
            </div>
        </Form>
    );
}
