import { Head, Link, router, usePage } from "@inertiajs/react";
import { Filter, Inbox, Plus, Search } from "lucide-react";
import { useState } from "react";
import { EmptyState } from "@/components/empty-state";
import Heading from "@/components/heading";
import {
	PriorityBadge,
	priorityLabels,
	StatusBadge,
	statusLabels,
} from "@/components/ticket-badges";
import AppLayout from "@/layouts/app-layout";
import {
	Table,
	TableBody,
	TableCell,
	TableHead,
	TableHeader,
	TableRow,
} from "@/components/ui/table";
import type { Paginated, Ticket, TicketPriority, TicketStatus } from "@/types";

type TicketIndexProps = {
	tickets: Paginated<Ticket>;
	filters: {
		search: string;
		status: string;
		priority: string;
	};
};

export default function Index() {
	const { tickets, filters } = usePage<TicketIndexProps>().props;
	const [search, setSearch] = useState(filters.search);
	const [status, setStatus] = useState(filters.status);
	const [priority, setPriority] = useState(filters.priority);

	function applyFilters(event: React.FormEvent<HTMLFormElement>): void {
		event.preventDefault();
		router.get(
			"/tickets",
			{ search, status, priority },
			{ preserveState: true, replace: true },
		);
	}

	return (
		<>
			<Head title="Tickets" />
			<div className="flex flex-1 flex-col gap-6 p-6">
				<div className="flex flex-wrap items-start justify-between gap-4">
					<Heading
						title="Tickets"
						description="Track every customer request from first message to resolution."
					/>
					<Link
						href="/tickets/create"
						className="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-transform hover:-translate-y-0.5 focus-visible:ring-2 focus-visible:ring-ring"
					>
						<Plus className="size-4" /> New ticket
					</Link>
				</div>

				<form
					onSubmit={applyFilters}
					className="flex flex-wrap items-center gap-3 rounded-xl border border-border bg-card p-4"
				>
					<div className="relative min-w-0 flex-1 basis-full md:min-w-60 md:basis-auto">
						<Search className="pointer-events-none absolute top-2.5 left-3 size-4 text-muted-foreground" />
						<input
							value={search}
							onChange={(event) => setSearch(event.target.value)}
							placeholder="Search tickets"
							className="h-9 w-full rounded-md border border-input bg-transparent pr-3 pl-9 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
						/>
					</div>
					<select
						value={status}
						onChange={(event) => setStatus(event.target.value)}
						className="h-9 rounded-md border border-input bg-background px-3 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
					>
						<option value="">All statuses</option>
						{(Object.keys(statusLabels) as TicketStatus[]).map(
							(value) => (
								<option key={value} value={value}>
									{statusLabels[value]}
								</option>
							),
						)}
					</select>
					<select
						value={priority}
						onChange={(event) => setPriority(event.target.value)}
						className="h-9 rounded-md border border-input bg-background px-3 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
					>
						<option value="">All priorities</option>
						{(Object.keys(priorityLabels) as TicketPriority[]).map(
							(value) => (
								<option key={value} value={value}>
									{priorityLabels[value]}
								</option>
							),
						)}
					</select>
					<button
						type="submit"
						className="inline-flex h-9 items-center gap-2 rounded-md border border-input px-3 text-sm font-medium transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring"
					>
						<Filter className="size-4" /> Apply
					</button>
				</form>

				<section className="overflow-hidden rounded-xl border border-border bg-card">
					{tickets.data.length === 0
						? (
							<EmptyState
								icon={Inbox}
								title="No tickets found"
								description="Try adjusting the filters or create a new ticket."
								action={
									<Link
										href="/tickets/create"
										className="text-sm font-medium text-primary underline underline-offset-4"
									>
										Create a ticket
									</Link>
								}
							/>
						)
						: (
							<Table>
								<TableHeader>
									<TableRow className="bg-muted/30 hover:bg-muted/30">
										<TableHead>Ticket</TableHead>
										<TableHead>Status</TableHead>
										<TableHead>Priority</TableHead>
										<TableHead>Assignee</TableHead>
									</TableRow>
								</TableHeader>
								<TableBody>
									{tickets.data.map((ticket) => (
										<TableRow
											key={ticket.id}
										>
											<TableCell>
												<Link
													href={`/tickets/${ticket.id}`}
													className="block min-w-48"
												>
													<p className="truncate font-medium">
														{ticket.title}
													</p>
													<p className="mt-1 truncate text-xs text-muted-foreground">
														#{ticket.id} ·{" "}
														{ticket.category
															?.name ??
															"Uncategorized"}
													</p>
												</Link>
											</TableCell>
											<TableCell>
												<StatusBadge
													status={ticket.status}
												/>
											</TableCell>
											<TableCell>
												<PriorityBadge
													priority={ticket.priority}
												/>
											</TableCell>
											<TableCell className="text-xs text-muted-foreground">
												{ticket.assignee?.name ??
													"Unassigned"}
											</TableCell>
										</TableRow>
									))}
								</TableBody>
							</Table>
						)}
				</section>

				{tickets.last_page > 1 && (
					<nav
						className="flex flex-wrap gap-2"
						aria-label="Pagination"
					>
						{tickets.links.map(
							(link) =>
								link.url && (
									<Link
										key={link.label}
										href={link.url}
										className={`rounded-md border px-3 py-1.5 text-sm ${
											link.active
												? "border-primary bg-primary text-primary-foreground"
												: "border-border hover:bg-muted"
										}`}
										dangerouslySetInnerHTML={{
											__html: link.label,
										}}
									/>
								),
						)}
					</nav>
				)}
			</div>
		</>
	);
}

Index.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
