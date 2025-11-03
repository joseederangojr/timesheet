import { usePage } from '@inertiajs/react';

/**
 * Hook to merge old input data with form default values.
 * Old input takes precedence over provided defaults.
 *
 * @param defaults - Default values for the form fields
 * @returns Merged values with old input taking precedence
 */
export function useFormDefaults<T extends Record<string, unknown>>(
    defaults: T,
): T {
    const { props } = usePage();
    const oldInput = (props.old as Partial<T>) || {};

    // Merge old input with defaults, old input takes precedence
    return { ...defaults, ...oldInput };
}
