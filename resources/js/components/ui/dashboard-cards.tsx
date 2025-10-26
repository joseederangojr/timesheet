import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { type ReactNode } from 'react';

interface DashboardCardProps {
    title: string;
    description?: string;
    children: ReactNode;
    className?: string;
}

export function DashboardCard({
    title,
    description,
    children,
    className,
}: DashboardCardProps) {
    return (
        <Card className={className}>
            <CardHeader>
                <CardTitle>{title}</CardTitle>
                {description && (
                    <CardDescription>{description}</CardDescription>
                )}
            </CardHeader>
            <CardContent>{children}</CardContent>
        </Card>
    );
}

interface StatsCardProps {
    title: string;
    value: string | number;
    description?: string;
    className?: string;
    valueClassName?: string;
}

export function StatsCard({
    title,
    value,
    description,
    className,
    valueClassName,
}: StatsCardProps) {
    return (
        <Card className={className}>
            <CardHeader>
                <CardTitle className="text-sm font-medium">{title}</CardTitle>
            </CardHeader>
            <CardContent>
                <div className={cn('text-2xl font-bold', valueClassName)}>
                    {value}
                </div>
                {description && (
                    <p className="text-sm text-muted-foreground">
                        {description}
                    </p>
                )}
            </CardContent>
        </Card>
    );
}

interface CardGridProps {
    children: ReactNode;
    columns?: 1 | 2 | 3 | 4;
    className?: string;
}

export function CardGrid({ 
    children, 
    columns = 3, 
    className 
}: CardGridProps) {
    const gridCols = {
        1: 'grid-cols-1',
        2: 'md:grid-cols-2',
        3: 'md:grid-cols-2 lg:grid-cols-3',
        4: 'md:grid-cols-2 lg:grid-cols-4',
    };

    return (
        <div className={cn('grid gap-6', gridCols[columns], className)}>
            {children}
        </div>
    );
}