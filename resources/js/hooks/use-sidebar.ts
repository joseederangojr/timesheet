import { setCookie } from '@/lib/cookies';
import { usePage } from '@inertiajs/react';
import * as React from 'react';

interface SharedProps {
    metadata: {
        sidebar: 0 | 1;
    };
    [key: string]: unknown;
}

export function useSidebar() {
    const { props } = usePage<SharedProps>();
    const [sidebarCollapsed, setSidebarCollapsedState] = React.useState(
        props.metadata.sidebar === 1,
    );

    const setSidebarCollapsed = (collapsed: boolean) => {
        setSidebarCollapsedState(collapsed);
        setCookie('sidebar', collapsed ? '1' : '0');
    };

    const toggleSidebar = () => {
        setSidebarCollapsed(!sidebarCollapsed);
    };

    return {
        sidebarCollapsed,
        setSidebarCollapsed,
        toggleSidebar,
    };
}
