import { Search, X } from 'lucide-react';
import * as React from 'react';

import {
    InputGroup,
    InputGroupAddon,
    InputGroupButton,
    InputGroupInput,
} from '@/components/ui/input-group';

interface SearchInputProps extends React.ComponentProps<typeof InputGroup> {
    placeholder?: string;
    value: string;
    onValueChange: (value: string) => void;
    debounceDelay?: number;
}

export function SearchInput({
    placeholder = 'Search...',
    value,
    onValueChange,
    debounceDelay = 300,
    ...props
}: SearchInputProps) {
    const [localValue, setLocalValue] = React.useState(value);

    React.useEffect(() => {
        const timer = setTimeout(() => {
            if (localValue !== value) {
                onValueChange(localValue);
            }
        }, debounceDelay);

        return () => clearTimeout(timer);
    }, [localValue, onValueChange, debounceDelay, value]);

    return (
        <InputGroup {...props}>
            <InputGroupInput
                placeholder={placeholder}
                value={localValue}
                onChange={(e) => setLocalValue(e.target.value)}
            />
            <InputGroupAddon>
                <Search />
            </InputGroupAddon>
            {localValue && (
                <InputGroupAddon align="inline-end">
                    <InputGroupButton
                        variant="ghost"
                        size="icon-xs"
                        onClick={() => setLocalValue('')}
                    >
                        <X />
                    </InputGroupButton>
                </InputGroupAddon>
            )}
        </InputGroup>
    );
}
