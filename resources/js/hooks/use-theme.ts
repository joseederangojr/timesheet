import { usePage } from '@inertiajs/react';
import { useCallback, useEffect, useState } from 'react';

export type Theme = 'light' | 'dark' | 'system';

interface SharedProps {
    metadata: {
        theme: Theme;
        sidebar: {
            collapsed: boolean;
        };
    };
    [key: string]: unknown;
}

const prefersDark = () => {
    if (typeof window === 'undefined') {
        return false;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
};

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const applyTheme = (theme: Theme) => {
    const isDark = theme === 'dark' || (theme === 'system' && prefersDark());

    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
};

const mediaQuery = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.matchMedia('(prefers-color-scheme: dark)');
};

const handleSystemThemeChange = () => {
    const currentTheme = localStorage.getItem('theme') as Theme;
    applyTheme(currentTheme || 'system');
};

export function initializeTheme() {
    const savedTheme = (localStorage.getItem('theme') as Theme) || 'system';

    applyTheme(savedTheme);

    // Add the event listener for system theme changes...
    mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

export function useTheme() {
    const { props } = usePage<SharedProps>();
    const [theme, setTheme] = useState<Theme>(props.metadata.theme);

    const updateTheme = useCallback((mode: Theme) => {
        setTheme(mode);

        // Store in localStorage for client-side persistence...
        localStorage.setItem('theme', mode);

        // Store in cookie for SSR...
        setCookie('theme', mode);

        applyTheme(mode);
    }, []);

    useEffect(() => {
        // Use the shared prop as the initial value, but allow localStorage to override
        const savedTheme = localStorage.getItem('theme') as Theme | null;
        const initialTheme = savedTheme || props.metadata.theme;

        if (initialTheme !== theme) {
            updateTheme(initialTheme);
        }

        return () =>
            mediaQuery()?.removeEventListener(
                'change',
                handleSystemThemeChange,
            );
    }, [updateTheme, props.metadata.theme, theme]);

    return { theme, updateTheme } as const;
}
