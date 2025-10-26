interface SharedMetadata {
    sidebar: 1 | 0;
    theme: 'system' | 'light' | 'dark';
}

interface AuthUser {
    id: number;
    name: string;
    email: string;
}

interface Auth {
    user: AuthUser;
}

export interface SharedData {
    name: string;
    auth?: Auth;
    metadata: SharedMetadata;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}
