import { Alert, AlertDescription } from "@/components/ui/alert";
import { usePage } from "@inertiajs/react";
import { CheckCircle, XCircle, AlertTriangle, Info, HelpCircle } from "lucide-react";

interface StatusData {
    type?: string;
    message?: string;
}

interface PageProps {
    status?: StatusData | null;
    [key: string]: unknown;
}

interface FlashStatusMessageProps {
    className?: string;
}

export function FlashStatusMessage({ className }: FlashStatusMessageProps) {
    const { props } = usePage<PageProps>();

    const status = props.status;

    if (!status || !status.message) {
        return null;
    }

    const { type, message } = status;

    const variants = {
        success: {
            className: "border-green-200 bg-green-50 text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200",
            icon: CheckCircle,
        },
        error: {
            className: "border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200",
            icon: XCircle,
        },
        warning: {
            className: "border-yellow-200 bg-yellow-50 text-yellow-800 dark:border-yellow-800 dark:bg-yellow-950 dark:text-yellow-200",
            icon: AlertTriangle,
        },
        info: {
            className: "border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-200",
            icon: Info,
        },
        greeting: {
            className: "border-gray-200 bg-gray-50 text-gray-800 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-200",
            icon: CheckCircle,
        },
        default: {
            className: "border-gray-200 bg-gray-50 text-gray-800 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-200",
            icon: HelpCircle,
        },
    };

    const variant = variants[type as keyof typeof variants] || variants.default;
    const Icon = variant.icon;

    return (
        <div className={className}>
            <Alert className={variant.className}>
                <Icon className="h-4 w-4" />
                <AlertDescription>{message}</AlertDescription>
            </Alert>
        </div>
    );
}

// Keep the old name for backward compatibility
export const FlashMessage = FlashStatusMessage;
