import type { UserRole } from './auth';
import type { Ticket } from './tickets';

export type DashboardStats = {
    readonly total: number;
    readonly open: number;
    readonly in_progress: number;
    readonly waiting_on_client: number;
    readonly resolved: number;
    readonly urgent: number;
    readonly unassigned: number;
};

export type DashboardProps = {
    readonly role: UserRole;
    readonly metrics: DashboardMetrics;
    readonly recentTickets: Ticket[];
};

export type DashboardChartDatum = {
    readonly name: string;
    readonly value: number;
};

export type DashboardMetrics = DashboardStats & {
    readonly average_resolution_hours: number;
    readonly status_breakdown: DashboardChartDatum[];
    readonly priority_breakdown: DashboardChartDatum[];
};
