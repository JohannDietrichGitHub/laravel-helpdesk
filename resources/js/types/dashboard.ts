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
    readonly stats: DashboardStats;
    readonly recentTickets: Ticket[];
};
