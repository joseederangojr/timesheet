import { Check, ChevronsUpDown, X } from 'lucide-react';
import * as React from 'react';

import { Button } from '@/components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { ButtonGroup } from './ui/button-group';

interface Option {
    value: string;
    label: string;
}

interface ComboboxFilterProps extends React.ComponentProps<typeof Button> {
    options: Option[];
    value: string;
    onValueChange: (value: string) => void;
    placeholder: string;
    searchPlaceholder?: string;
    width?: string;
}

export function ComboboxFilter({
    options,
    value,
    onValueChange,
    placeholder,
    searchPlaceholder = 'Search...',
    width = 'w-32',
    className,
    ...props
}: ComboboxFilterProps) {
    const [open, setOpen] = React.useState(false);

    const handleClear = (e: React.MouseEvent) => {
        e.stopPropagation();
        onValueChange('');
    };

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <ButtonGroup>
                <PopoverTrigger asChild>
                    <Button
                        variant="outline"
                        role="combobox"
                        aria-expanded={open}
                        className={cn(width, 'justify-between', className)}
                        {...props}
                    >
                        {value
                            ? options.find((option) => option.value === value)
                                  ?.label
                            : placeholder}
                        <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                    </Button>
                </PopoverTrigger>
                {value && (
                    <Button variant="outline" size="icon" onClick={handleClear}>
                        <X />
                    </Button>
                )}
            </ButtonGroup>
            <PopoverContent className={cn(width, 'p-0')}>
                <Command>
                    <CommandInput
                        placeholder={searchPlaceholder}
                        className="h-9"
                    />
                    <CommandList>
                        <CommandEmpty>No item found.</CommandEmpty>
                        <CommandGroup>
                            {options.map((option) => (
                                <CommandItem
                                    key={option.value}
                                    value={option.value}
                                    onSelect={(currentValue) => {
                                        onValueChange(
                                            currentValue === value
                                                ? ''
                                                : currentValue,
                                        );
                                        setOpen(false);
                                    }}
                                >
                                    {option.label}
                                    <Check
                                        className={cn(
                                            'ml-auto h-4 w-4',
                                            value === option.value
                                                ? 'opacity-100'
                                                : 'opacity-0',
                                        )}
                                    />
                                </CommandItem>
                            ))}
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
