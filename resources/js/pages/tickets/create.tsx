import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import InputError from '@/components/input-error';
import AppLayout from '@/layouts/app-layout';
import type { TicketCategory } from '@/types';

type CreateProps = { categories: TicketCategory[] };

export default function Create({ categories }: CreateProps) {
    const form = useForm({
        title: '',
        description: '',
        category_id: '',
        priority: 'medium',
    });

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        form.post('/tickets');
    }

    return (
        <>
            <Head title="New ticket" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center gap-3">
                    <Link
                        href="/tickets"
                        className="rounded-md p-2 text-muted-foreground hover:bg-muted hover:text-foreground"
                        aria-label="Back to tickets"
                    >
                        <ArrowLeft className="size-4" />
                    </Link>
                    <div>
                        <p className="text-sm text-muted-foreground">Tickets</p>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Create a ticket
                        </h1>
                    </div>
                </div>
                <form
                    onSubmit={submit}
                    className="max-w-2xl space-y-5 rounded-xl border border-border bg-card p-6"
                >
                    <div className="space-y-2">
                        <label htmlFor="title" className="text-sm font-medium">
                            Title
                        </label>
                        <input
                            id="title"
                            value={form.data.title}
                            onChange={(event) =>
                                form.setData('title', event.target.value)
                            }
                            className="h-10 w-full rounded-md border border-input bg-transparent px-3 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            required
                        />
                        <InputError message={form.errors.title} />
                    </div>
                    <div className="space-y-2">
                        <label
                            htmlFor="description"
                            className="text-sm font-medium"
                        >
                            Describe the issue
                        </label>
                        <textarea
                            id="description"
                            value={form.data.description}
                            onChange={(event) =>
                                form.setData('description', event.target.value)
                            }
                            className="min-h-40 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            required
                        />
                        <InputError message={form.errors.description} />
                    </div>
                    <div className="grid gap-5 sm:grid-cols-2">
                        <div className="space-y-2">
                            <label
                                htmlFor="category_id"
                                className="text-sm font-medium"
                            >
                                Category
                            </label>
                            <select
                                id="category_id"
                                value={form.data.category_id}
                                onChange={(event) =>
                                    form.setData(
                                        'category_id',
                                        event.target.value,
                                    )
                                }
                                className="h-10 w-full rounded-md border border-input bg-background px-3 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option value="">Select a category</option>
                                {categories.map((category) => (
                                    <option
                                        key={category.id}
                                        value={category.id}
                                    >
                                        {category.name}
                                    </option>
                                ))}
                            </select>
                            <InputError message={form.errors.category_id} />
                        </div>
                        <div className="space-y-2">
                            <label
                                htmlFor="priority"
                                className="text-sm font-medium"
                            >
                                Priority
                            </label>
                            <select
                                id="priority"
                                value={form.data.priority}
                                onChange={(event) =>
                                    form.setData('priority', event.target.value)
                                }
                                className="h-10 w-full rounded-md border border-input bg-background px-3 text-sm outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            <InputError message={form.errors.priority} />
                        </div>
                    </div>
                    <div className="flex items-center justify-end gap-3">
                        <Link
                            href="/tickets"
                            className="rounded-md px-4 py-2 text-sm font-medium text-muted-foreground hover:bg-muted"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={form.processing}
                            className="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground disabled:opacity-50"
                        >
                            {form.processing ? 'Creating…' : 'Create ticket'}
                        </button>
                    </div>
                </form>
            </div>
        </>
    );
}

Create.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
