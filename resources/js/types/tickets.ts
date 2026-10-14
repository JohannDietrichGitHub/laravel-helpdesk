export type TicketStatus =
    | 'open'
    | 'in_progress'
    | 'waiting_on_client'
    | 'resolved'
    | 'closed';

export type TicketPriority = 'low' | 'medium' | 'high' | 'urgent';

export type TicketUser = {
    id: number;
    name: string;
    email?: string;
    role?: string;
};

export type TicketCategory = {
    id: number;
    name: string;
};

export type TicketMessage = {
    id: number;
    body: string;
    is_internal?: boolean;
    created_at?: string;
    user: TicketUser | null;
};

export type Ticket = {
    id: number;
    title: string;
    description: string;
    status: TicketStatus;
    priority: TicketPriority;
    resolved_at: string | null;
    last_reply_at: string | null;
    user: TicketUser;
    assignee: TicketUser | null;
    category: TicketCategory | null;
    messages?: TicketMessage[];
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
};
