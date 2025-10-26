import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import { Form } from '@inertiajs/react';
import { Key, Mail } from 'lucide-react';

interface FormFieldProps {
    label: string;
    name: string;
    type?: string;
    placeholder?: string;
    error?: string;
    required?: boolean;
    className?: string;
}

export function FormField({
    label,
    name,
    type = 'text',
    placeholder,
    error,
    required,
    className,
}: FormFieldProps) {
    return (
        <div className={cn('space-y-2', className)}>
            <Label htmlFor={name}>{label}</Label>
            <Input
                id={name}
                name={name}
                type={type}
                placeholder={placeholder}
                required={required}
            />
            {error && (
                <p className="text-sm text-red-600 dark:text-red-400">
                    {error}
                </p>
            )}
        </div>
    );
}

interface LoginFormProps {
    action: string;
    title: string;
    description: string;
    icon: typeof Key | typeof Mail;
    buttonText: string;
    processingText: string;
    showPasswordField?: boolean;
}

export function LoginForm({
    action,
    title,
    description,
    icon: Icon,
    buttonText,
    processingText,
    showPasswordField = false,
}: LoginFormProps) {
    return (
        <Form action={action} method="post" resetOnSuccess={false}>
            {({ errors, processing }) => (
                <>
                    <div className="mb-6 text-center">
                        <div className="flex items-center justify-center gap-2 text-2xl font-bold">
                            <Icon className="h-6 w-6" />
                            {title}
                        </div>
                        <p className="mt-2 text-muted-foreground">
                            {description}
                        </p>
                    </div>

                    <div className="space-y-4">
                        <FormField
                            label="Email"
                            name="email"
                            type="email"
                            placeholder="Enter your email"
                            error={errors.email}
                            required
                        />

                        {showPasswordField && (
                            <FormField
                                label="Password"
                                name="password"
                                type="password"
                                placeholder="Enter your password"
                                error={errors.password}
                                required
                            />
                        )}

                        <Button
                            type="submit"
                            className="w-full"
                            disabled={processing}
                        >
                            {processing ? processingText : buttonText}
                        </Button>
                    </div>
                </>
            )}
        </Form>
    );
}
