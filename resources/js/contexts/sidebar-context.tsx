import { useSidebar } from '@/hooks/use-sidebar';
import { createContext, useContext, type ReactNode } from 'react';

interface SidebarContextValue {
    collapsed: boolean;
    toggle: () => void;
}

const SidebarContext = createContext<SidebarContextValue | undefined>(
    undefined,
);

interface SidebarProviderProps {
    children: ReactNode;
}

export function SidebarProvider({ children }: SidebarProviderProps) {
    const { sidebarCollapsed, toggleSidebar } = useSidebar();

    return (
        <SidebarContext.Provider
            value={{ collapsed: sidebarCollapsed, toggle: toggleSidebar }}
        >
            {children}
        </SidebarContext.Provider>
    );
}

export function useSidebarContext() {
    const context = useContext(SidebarContext);
    if (context === undefined) {
        throw new Error(
            'useSidebarContext must be used within a SidebarProvider',
        );
    }
    return context;
}
