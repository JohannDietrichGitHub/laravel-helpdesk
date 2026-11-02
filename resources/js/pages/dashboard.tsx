import { Head, Link } from "@inertiajs/react";
import { EmptyState } from "@/components/empty-state";
import { PriorityBadge, StatusBadge } from "@/components/ticket-badges";
import { dashboard } from "@/routes";
import type { DashboardProps } from "@/types";

const roleContent = {
	client: {
		title: "Your support overview",
		description: "Track your requests and follow the latest replies.",
		recentTitle: "Your recent tickets",
		cards: [
			["total", "Total tickets"],
			["open", "Open"],
			["waiting_on_client", "Waiting for you"],
			["resolved", "Resolved"],
		],
	},
	agent: {
		title: "Your work queue",
		description: "Focus on the tickets assigned to you.",
		recentTitle: "Recently updated assignments",
		cards: [
			["total", "Assigned tickets"],
			["open", "Open"],
			["in_progress", "In progress"],
			["urgent", "Urgent"],
		],
	},
	admin: {
		title: "Operations overview",
		description:
			"Monitor the full support queue and workload distribution.",
		recentTitle: "Recently updated tickets",
		cards: [
			["total", "All tickets"],
			["unassigned", "Unassigned"],
			["open", "Open"],
			["urgent", "Urgent"],
		],
	},
} as const;

export default function Dashboard(
	{ role, stats, recentTickets }: DashboardProps,
) {
	const content = roleContent[role];

	return (
		<>
			<Head title="Dashboard" />
			<div className="flex flex-1 flex-col gap-6 p-4 sm:p-6">
				<header>
					<h1 className="text-2xl font-semibold tracking-tight">
						{content.title}
					</h1>
					<p className="mt-1 text-sm text-muted-foreground">
						{content.description}
					</p>
				</header>

				<section
					className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
					aria-label="Dashboard summary"
				>
					{content.cards.map(([key, label]) => (
						<article
							key={key}
							className="rounded-xl border border-border bg-card p-5"
						>
							<p className="text-sm text-muted-foreground">
								{label}
							</p>
							<p className="mt-2 text-3xl font-semibold tracking-tight">
								{stats[key]}
							</p>
						</article>
					))}
				</section>

				<section className="overflow-hidden rounded-xl border border-border bg-card">
					<div className="flex items-center justify-between gap-4 border-b border-border px-5 py-4">
						<h2 className="font-semibold">{content.recentTitle}</h2>
						<Link
							href="/tickets"
							className="text-sm font-medium text-primary underline underline-offset-4"
						>
							View all
						</Link>
					</div>
					{recentTickets.length === 0
						? (
							<EmptyState
								title="No tickets yet"
								description="New tickets will appear here when they are available."
							/>
						)
						: (
							<div className="divide-y divide-border">
								{recentTickets.map((ticket) => (
									<Link
										key={ticket.id}
										href={`/tickets/${ticket.id}`}
										className="flex flex-wrap items-center justify-between gap-3 px-5 py-4 transition-colors hover:bg-muted/40"
									>
										<div className="min-w-0">
											<p className="truncate text-sm font-medium">
												{ticket.title}
											</p>
											<p className="mt-1 text-xs text-muted-foreground">
												#{ticket.id} ·{" "}
												{ticket.assignee?.name ??
													"Unassigned"}
											</p>
										</div>
										<div className="flex shrink-0 items-center gap-3">
											<StatusBadge
												status={ticket.status}
											/>
											<PriorityBadge
												priority={ticket.priority}
											/>
										</div>
									</Link>
								))}
							</div>
						)}
				</section>
			</div>
		</>
	);
}

Dashboard.layout = {
	breadcrumbs: [
		{
			title: "Dashboard",
			href: dashboard(),
		},
	],
};
