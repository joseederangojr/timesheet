import { createContext, useContext, type ReactNode } from 'react';

interface User {
    name: string;
    email: string;
}

interface AuthUser {
    user: User;
}

interface AuthContextValue {
    auth: AuthUser;
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

interface AuthProviderProps {
    auth: AuthUser;
    children: ReactNode;
}

export function AuthProvider({ auth, children }: AuthProviderProps) {
    return (
        <AuthContext.Provider value={{ auth }}>{children}</AuthContext.Provider>
    );
}

export function useAuth() {
    const context = useContext(AuthContext);
    if (context === undefined) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
}
