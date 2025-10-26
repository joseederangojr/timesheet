import { Button } from '@/components/ui/button';
import { useAuth } from '@/contexts/auth-context';
import { cn } from '@/lib/utils';
import { Form } from '@inertiajs/react';
import { LogOut } from 'lucide-react';
import { type ReactNode } from 'react';

interface AppLayoutProps {
    children: ReactNode;
    className?: string;
}

export function AppLayout({ children, className }: AppLayoutProps) {
    const { auth } = useAuth();

    return (
        <div className={cn('min-h-screen bg-background', className)}>
            <header className="border-b bg-card px-6 py-4">
                <div className="mx-auto flex max-w-7xl items-center justify-between">
                    <h1 className="text-xl font-bold">Timesheet</h1>
                    <div className="flex items-center gap-4">
                        <span className="text-sm text-muted-foreground">
                            {auth.user.name}
                        </span>
                        <Form action="/auth/session" method="delete">
                            {({ processing }) => (
                                <Button
                                    type="submit"
                                    variant="outline"
                                    size="sm"
                                    disabled={processing}
                                    className="flex items-center gap-2"
                                >
                                    <LogOut className="h-4 w-4" />
                                    {processing ? 'Signing out...' : 'Sign Out'}
                                </Button>
                            )}
                        </Form>
                    </div>
                </div>
            </header>
            <main className="p-6">
                <div className="mx-auto max-w-7xl">{children}</div>
            </main>
        </div>
    );
}
