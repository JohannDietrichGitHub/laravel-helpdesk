import { Head, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import type { Ticket } from '@/types';

export default function Show({
    ticket,
}: {
    ticket: Ticket & { messages: NonNullable<Ticket['messages']> };
}) {
    const form = useForm<{ body: string }>({ body: '' });

    function submitReply(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        form.post(`/tickets/${ticket.id}/messages`, { preserveScroll: true });
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
                        {ticket.status} · {ticket.priority}
                    </p>
                </header>

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
                    <Button type="submit" disabled={form.processing}>
                        Send reply
                    </Button>
                </form>
            </div>
        </>
    );
}

Show.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
