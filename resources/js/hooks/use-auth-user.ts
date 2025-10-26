import { usePage } from '@inertiajs/react';

interface AuthUser {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
    created_at: string;
    updated_at: string;
}

interface SharedProps {
    auth: AuthUser | null;
    [key: string]: unknown;
}

export function useAuthUser() {
    const { props } = usePage<SharedProps>();
    return props.auth;
}
