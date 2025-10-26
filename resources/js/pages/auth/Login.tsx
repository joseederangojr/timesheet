import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Form } from '@inertiajs/react';
import { Key, Mail } from 'lucide-react';
import { useState } from 'react';

interface LoginProps {
    message?: string;
}

export default function Login({ message }: LoginProps) {
    const [usePassword, setUsePassword] = useState(false);

    return (
        <div className="flex min-h-screen items-center justify-center bg-background p-4">
            <Card className="w-full max-w-md">
                <CardHeader className="text-center">
                    <CardTitle className="flex items-center justify-center gap-2 text-2xl font-bold">
                        {usePassword ? (
                            <>
                                <Key className="h-6 w-6" />
                                Sign In with Password
                            </>
                        ) : (
                            <>
                                <Mail className="h-6 w-6" />
                                Sign In with Magic Link
                            </>
                        )}
                    </CardTitle>
                    <CardDescription>
                        {usePassword
                            ? 'Enter your email and password to sign in'
                            : 'Enter your email to receive a magic link'}
                    </CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                    {message && (
                        <div className="rounded-md bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/20 dark:text-green-400">
                            {message}
                        </div>
                    )}

                    {usePassword ? (
                        <Form
                            action="/auth/password"
                            method="post"
                            resetOnSuccess={false}
                        >
                            {({ errors, processing }) => (
                                <>
                                    <div className="space-y-2">
                                        <Label htmlFor="email">Email</Label>
                                        <Input
                                            id="email"
                                            name="email"
                                            type="email"
                                            placeholder="Enter your email"
                                            required
                                        />
                                        {errors.email && (
                                            <p className="text-sm text-red-600 dark:text-red-400">
                                                {errors.email}
                                            </p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="password">
                                            Password
                                        </Label>
                                        <Input
                                            id="password"
                                            name="password"
                                            type="password"
                                            placeholder="Enter your password"
                                            required
                                        />
                                        {errors.password && (
                                            <p className="text-sm text-red-600 dark:text-red-400">
                                                {errors.password}
                                            </p>
                                        )}
                                    </div>

                                    <Button
                                        type="submit"
                                        className="w-full"
                                        disabled={processing}
                                    >
                                        {processing
                                            ? 'Signing in...'
                                            : 'Sign In'}
                                    </Button>
                                </>
                            )}
                        </Form>
                    ) : (
                        <Form
                            action="/auth/magic-link"
                            method="post"
                            resetOnSuccess={false}
                        >
                            {({ errors, processing }) => (
                                <>
                                    <div className="space-y-2">
                                        <Label htmlFor="email">Email</Label>
                                        <Input
                                            id="email"
                                            name="email"
                                            type="email"
                                            placeholder="Enter your email"
                                            required
                                        />
                                        {errors.email && (
                                            <p className="text-sm text-red-600 dark:text-red-400">
                                                {errors.email}
                                            </p>
                                        )}
                                    </div>

                                    <Button
                                        type="submit"
                                        className="w-full"
                                        disabled={processing}
                                    >
                                        {processing
                                            ? 'Sending Magic Link...'
                                            : 'Send Magic Link'}
                                    </Button>
                                </>
                            )}
                        </Form>
                    )}

                    <div className="flex items-center justify-center space-x-2 pt-4">
                        <Label
                            htmlFor="login-method-toggle"
                            className="text-sm font-medium"
                        >
                            {usePassword ? 'Use Magic Link' : 'Use Password'}
                        </Label>
                        <Switch
                            id="login-method-toggle"
                            checked={usePassword}
                            onCheckedChange={setUsePassword}
                        />
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}
