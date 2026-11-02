import { Head, useForm, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { PriorityBadge, StatusBadge } from '@/components/ticket-badges';
import AppLayout from '@/layouts/app-layout';
import type { Auth, Ticket, TicketUser } from '@/types';

type ShowProps = {
    auth: Auth;
    ticket: Ticket & { messages: NonNullable<Ticket['messages']> };
    agents: TicketUser[];
};

export default function Show({ ticket, agents }: ShowProps) {
    const { auth } = usePage<ShowProps>().props;
    const canChangeStatus =
        auth.user.role === 'admin' || ticket.assignee?.id === auth.user.id;
    const canAssign = auth.user.role === 'admin';
    const form = useForm<{ body: string; attachments: File[] }>({
        body: '',
        attachments: [],
    });
    const statusForm = useForm({ status: ticket.status });
    const assignmentForm = useForm({ assigned_to: ticket.assignee?.id ?? '' });

    function submitReply(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        form.post(`/tickets/${ticket.id}/messages`, {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => form.reset('body', 'attachments'),
        });
    }

    function updateStatus(status: Ticket['status']): void {
        statusForm.transform(() => ({ status }));
        statusForm.patch(`/tickets/${ticket.id}/status`, {
            preserveScroll: true,
        });
        statusForm.setData('status', status);
    }

    function assign(event: React.ChangeEvent<HTMLSelectElement>): void {
        const assignedTo = Number(event.target.value);
        assignmentForm.transform(() => ({ assigned_to: assignedTo }));
        assignmentForm.patch(`/tickets/${ticket.id}/assignment`, {
            preserveScroll: true,
        });
        assignmentForm.setData('assigned_to', assignedTo);
    }

    return (
        <>
            <Head title={ticket.title} />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <header>
                    <p className="text-sm text-muted-foreground">
                        Ticket #{ticket.id}
                    </p>
                    <h1 className="mt-1 text-2xl font-semibold tracking-tight">
                        {ticket.title}
                    </h1>
                    <p className="mt-2 text-sm text-muted-foreground">
                        <StatusBadge status={ticket.status} />{' '}
                        <PriorityBadge priority={ticket.priority} />
                    </p>
                </header>

                {(canChangeStatus || canAssign) && (
                    <section className="flex flex-wrap items-end gap-4 rounded-xl border border-border bg-card p-5">
                        {canChangeStatus && (
                            <div className="space-y-2">
                                <label
                                    htmlFor="status"
                                    className="text-sm font-medium"
                                >
                                    Status
                                </label>
                                <select
                                    id="status"
                                    value={statusForm.data.status}
                                    onChange={(event) =>
                                        updateStatus(
                                            event.target
                                                .value as Ticket['status'],
                                        )
                                    }
                                    disabled={statusForm.processing}
                                    className="h-9 rounded-md border border-input bg-background px-3 text-sm"
                                >
                                    <option value="open">Open</option>
                                    <option value="in_progress">
                                        In progress
                                    </option>
                                    <option value="waiting_on_client">
                                        Waiting on client
                                    </option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        )}
                        {canAssign && (
                            <div className="space-y-2">
                                <label
                                    htmlFor="assigned_to"
                                    className="text-sm font-medium"
                                >
                                    Assignee
                                </label>
                                <select
                                    id="assigned_to"
                                    value={assignmentForm.data.assigned_to}
                                    onChange={assign}
                                    disabled={assignmentForm.processing}
                                    className="h-9 rounded-md border border-input bg-background px-3 text-sm"
                                >
                                    <option value="">Select an agent</option>
                                    {agents.map((agent) => (
                                        <option key={agent.id} value={agent.id}>
                                            {agent.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        )}
                    </section>
                )}

                <section className="rounded-xl border border-border bg-card p-5">
                    <p className="text-sm leading-6 whitespace-pre-wrap">
                        {ticket.description}
                    </p>
                </section>

                <section className="space-y-4">
                    <h2 className="text-lg font-semibold">Conversation</h2>
                    {ticket.messages.map((message) => (
                        <article
                            key={message.id}
                            className="rounded-xl border border-border bg-card p-4"
                        >
                            <p className="text-xs font-medium text-muted-foreground">
                                {message.user?.name ?? 'System'}
                            </p>
                            <p className="mt-2 text-sm leading-6 whitespace-pre-wrap">
                                {message.body}
                            </p>
                            {message.attachments.length > 0 && (
                                <ul className="mt-3 space-y-1 text-sm">
                                    {message.attachments.map((attachment) => (
                                        <li key={attachment.id}>
                                            <a
                                                className="text-primary underline"
                                                href={`/attachments/${attachment.id}/download`}
                                            >
                                                {attachment.original_name}
                                            </a>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </article>
                    ))}
                </section>

                <form
                    onSubmit={submitReply}
                    className="space-y-3 rounded-xl border border-border bg-card p-5"
                >
                    <label htmlFor="body" className="text-sm font-medium">
                        Reply
                    </label>
                    <textarea
                        id="body"
                        value={form.data.body}
                        onChange={(event) =>
                            form.setData('body', event.target.value)
                        }
                        className="min-h-32 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        required
                    />
                    <input
                        type="file"
                        multiple
                        onChange={(event) =>
                            form.setData(
                                'attachments',
                                Array.from(event.target.files ?? []),
                            )
                        }
                        className="block w-full text-sm"
                        accept=".jpg,.jpeg,.png,.pdf,.txt,.zip"
                    />
                    <Button type="submit" disabled={form.processing}>
                        Send reply
                    </Button>
                </form>
            </div>
        </>
    );
}

Show.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
