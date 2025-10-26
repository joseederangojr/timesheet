import { setCookie } from '@/lib/cookies';
import { usePage } from '@inertiajs/react';
import { useCallback, useState } from 'react';

interface SharedProps {
    metadata: {
        sidebar: 0 | 1;
    };
    [key: string]: unknown;
}

export function useSidebar() {
    const { props } = usePage<SharedProps>();
    const [sidebarCollapsed, setSidebarCollapsedState] = useState(
        props.metadata.sidebar === 1,
    );

    const setSidebarCollapsed = useCallback((collapsed: boolean) => {
        setSidebarCollapsedState(collapsed);
        setCookie('sidebar', collapsed ? '1' : '0');
    }, []);

    const toggleSidebar = useCallback(() => {
        setSidebarCollapsed(!sidebarCollapsed);
    }, [sidebarCollapsed, setSidebarCollapsed]);

    return {
        sidebarCollapsed,
        setSidebarCollapsed,
        toggleSidebar,
    };
}
