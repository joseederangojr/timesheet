"use client";

import * as React from "react";
import {
	Cell,
	ColumnDef,
	flexRender,
	getCoreRowModel,
	getPaginationRowModel,
	getSortedRowModel,
	PaginationState,
	Row,
	SortingState,
	useReactTable,
	VisibilityState,
} from "@tanstack/react-table";
import { Checkbox } from "@/components/ui/checkbox";
import {
	Table,
	TableBody,
	TableCell,
	TableHead,
	TableHeader,
	TableRow,
} from "@/components/ui/table";

interface UseDataTableProps<T> {
	columns: ColumnDef<T>[];
	data: T[];
	pagination?: {
		current_page: number;
		last_page: number;
		per_page: number;
		total: number;
		links: unknown[];
	};
	sorting?: SortingState;
	onSortingChange?: (sorting: SortingState) => void;
	enableRowSelection?: boolean;
	onRowSelectionChange?: (selection: Record<string, boolean>) => void;
	searchPlaceholder?: string;
	enableColumnVisibility?: boolean;
	actions?: (row: T) => React.ReactNode;
	onPaginationChange?: (pagination?: PaginationState) => void;
}

export function useDataTable<T>({
	columns,
	data,
	pagination,
	sorting = [],
	onSortingChange,
	enableRowSelection = false,
	onRowSelectionChange,
	actions,
	onPaginationChange,
}: UseDataTableProps<T>) {
	const [sortingState, setSortingState] = React.useState<SortingState>(sorting);
	const [columnVisibility, setColumnVisibility] =
		React.useState<VisibilityState>({});
	const [rowSelection, setRowSelection] = React.useState({});
	const [paginationState, setPaginationState] = React.useState<PaginationState>(
		{
			pageIndex: (pagination?.current_page ?? 1) - 1,
			pageSize: pagination?.per_page ?? 10,
		},
	);

	const table = useReactTable<T>({
		data,
		columns: enableRowSelection
			? [
					{
						id: "select",
						header: ({ table }) => (
							<Checkbox
								checked={
									table.getIsAllPageRowsSelected() ||
									(table.getIsSomePageRowsSelected() && "indeterminate")
								}
								onCheckedChange={(value) =>
									table.toggleAllPageRowsSelected(!!value)
								}
								aria-label="Select all"
							/>
						),
						cell: ({ row }: { row: Row<T> }) => (
							<Checkbox
								checked={row.getIsSelected()}
								onCheckedChange={(value) => row.toggleSelected(!!value)}
								aria-label="Select row"
							/>
						),
						enableSorting: false,
						enableHiding: false,
					},
					...columns,
					...(actions
						? [
								{
									id: "actions",
									enableHiding: false,
									cell: ({ row }: { row: Row<T> }) => actions(row.original),
								},
							]
						: []),
				]
			: [
					...columns,
					...(actions
						? [
								{
									id: "actions",
									enableHiding: false,
									cell: ({ row }: { row: Row<T> }) => actions(row.original),
								},
							]
						: []),
				],
		onSortingChange: (updater) => {
			const newSorting =
				typeof updater === "function" ? updater(sortingState) : updater;
			setSortingState(newSorting);
			onSortingChange?.(newSorting);
		},
		onColumnVisibilityChange: setColumnVisibility,
		onRowSelectionChange: (updater) => {
			const newSelection =
				typeof updater === "function" ? updater(rowSelection) : updater;
			setRowSelection(newSelection);
			onRowSelectionChange?.(newSelection);
		},
		onPaginationChange: (updater) => {
			const newPagination =
				typeof updater === "function" ? updater(paginationState) : updater;
			setPaginationState(newPagination);
			onPaginationChange?.(newPagination);
		},
		getCoreRowModel: getCoreRowModel(),
		getPaginationRowModel: getPaginationRowModel(),
		getSortedRowModel: getSortedRowModel(),
		state: {
			sorting: sortingState,
			columnVisibility,
			rowSelection,
			pagination: paginationState,
		},
		manualPagination: !!pagination,
		pageCount: pagination ? pagination.last_page : undefined,
	});

	React.useEffect(() => {
		setSortingState(sorting);
	}, [sorting]);

	return table;
}

export const DataTableRoot = (props: React.PropsWithChildren) => {
	return <div className="w-full">{props.children}</div>;
};

export const DataTableHeader = (props: React.PropsWithChildren) => {
	return <div className="flex items-center py-4">{props.children}</div>;
};

export const DataTableContent = (props: React.PropsWithChildren) => {
	return (
		<div className="overflow-hidden rounded-md border">{props.children}</div>
	);
};

export const DataTableFooter = (props: React.PropsWithChildren) => {
	return <div className="flex py-4">{props.children}</div>;
};

interface DataTableProps<T> {
	table: ReturnType<typeof useDataTable<T>>;
}
export function DataTable<T>({ table }: DataTableProps<T>) {
	return (
		<Table>
			<TableHeader>
				{table.getHeaderGroups().map((headerGroup) => (
					<TableRow key={headerGroup.id}>
						{headerGroup.headers.map((header) => {
							return (
								<TableHead key={header.id}>
									{header.isPlaceholder
										? null
										: flexRender(
												header.column.columnDef.header,
												header.getContext(),
											)}
								</TableHead>
							);
						})}
					</TableRow>
				))}
			</TableHeader>
			<TableBody>
				{table.getRowModel().rows?.length ? (
					table.getRowModel().rows.map((row: Row<T>) => (
						<TableRow
							key={row.id}
							data-state={row.getIsSelected() && "selected"}
						>
							{row.getVisibleCells().map((cell: Cell<T, unknown>) => (
								<TableCell key={cell.id}>
									{flexRender(cell.column.columnDef.cell, cell.getContext())}
								</TableCell>
							))}
						</TableRow>
					))
				) : (
					<TableRow>
						<TableCell
							colSpan={table.getAllColumns().length}
							className="h-24 text-center"
						>
							No results.
						</TableCell>
					</TableRow>
				)}
			</TableBody>
		</Table>
	);
}
