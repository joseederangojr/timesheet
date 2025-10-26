import { cn } from '@/lib/utils';
import { AlertCircle, CheckCircle, Info, XCircle } from 'lucide-react';
import { type ReactNode } from 'react';

type AlertVariant = 'default' | 'success' | 'warning' | 'error';

interface AlertProps {
    variant?: AlertVariant;
    children: ReactNode;
    className?: string;
}

const alertVariants = {
    default: {
        container: 'bg-blue-50 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
        icon: Info,
    },
    success: {
        container: 'bg-green-50 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        icon: CheckCircle,
    },
    warning: {
        container: 'bg-yellow-50 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        icon: AlertCircle,
    },
    error: {
        container: 'bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-400',
        icon: XCircle,
    },
};

export function Alert({ 
    variant = 'default', 
    children, 
    className 
}: AlertProps) {
    const { container, icon: Icon } = alertVariants[variant];

    return (
        <div className={cn('rounded-md p-4 text-sm', container, className)}>
            <div className="flex items-start gap-3">
                <Icon className="h-4 w-4 mt-0.5 flex-shrink-0" />
                <div className="flex-1">{children}</div>
            </div>
        </div>
    );
}

interface MessageAlertProps {
    message: string;
    variant?: AlertVariant;
    className?: string;
}

export function MessageAlert({ 
    message, 
    variant = 'success', 
    className 
}: MessageAlertProps) {
    return (
        <Alert variant={variant} className={className}>
            {message}
        </Alert>
    );
}