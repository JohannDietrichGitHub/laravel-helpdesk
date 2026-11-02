import type { TicketPriority, TicketStatus } from '@/types';
import { Badge } from '@/components/ui/badge';

const statusLabels: Record<TicketStatus, string> = {
    open: 'Open',
    in_progress: 'In progress',
    waiting_on_client: 'Waiting on client',
    resolved: 'Resolved',
    closed: 'Closed',
};

const priorityLabels: Record<TicketPriority, string> = {
    low: 'Low',
    medium: 'Medium',
    high: 'High',
    urgent: 'Urgent',
};

const statusStyles: Record<TicketStatus, string> = {
    open: 'bg-sky-500/10 text-sky-700 dark:text-sky-300',
    in_progress: 'bg-amber-500/10 text-amber-700 dark:text-amber-300',
    waiting_on_client: 'bg-violet-500/10 text-violet-700 dark:text-violet-300',
    resolved: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300',
    closed: 'bg-muted text-muted-foreground',
};

const priorityStyles: Record<TicketPriority, string> = {
    low: 'text-muted-foreground',
    medium: 'text-sky-600 dark:text-sky-400',
    high: 'text-amber-600 dark:text-amber-400',
    urgent: 'font-semibold text-destructive dark:text-red-400',
};

export function StatusBadge({ status }: { status: TicketStatus }) {
    return (
        <Badge
            className={`w-fit rounded-md px-2 py-1 text-xs font-medium ${
                statusStyles[status]
            }`}
        >
            {statusLabels[status]}
        </Badge>
    );
}

export function PriorityBadge({ priority }: { priority: TicketPriority }) {
    return (
        <span className={`text-xs ${priorityStyles[priority]}`}>
            {priorityLabels[priority]}
        </span>
    );
}

export { priorityLabels, statusLabels };
