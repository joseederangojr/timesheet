import { LoginForm } from '@/components/forms/login-form';
import { MessageAlert } from '@/components/ui/alert';
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
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
                <CardContent className="space-y-4 pt-6">
                    {message && <MessageAlert message={message} />}

                    {usePassword ? (
                        <LoginForm
                            action="/auth/password"
                            title="Sign In with Password"
                            description="Enter your email and password to sign in"
                            icon={Key}
                            buttonText="Sign In"
                            processingText="Signing in..."
                            showPasswordField
                        />
                    ) : (
                        <LoginForm
                            action="/auth/magic-link"
                            title="Sign In with Magic Link"
                            description="Enter your email to receive a magic link"
                            icon={Mail}
                            buttonText="Send Magic Link"
                            processingText="Sending Magic Link..."
                        />
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
