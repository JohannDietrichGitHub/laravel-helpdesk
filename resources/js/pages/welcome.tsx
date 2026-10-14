import { Head, Link, usePage } from '@inertiajs/react';
import {
    ArrowUpRight,
    CheckCircle2,
    Clock3,
    Inbox,
    LifeBuoy,
    MessageSquare,
    ShieldCheck,
} from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';
import { dashboard, login, register } from '@/routes';

const queueItems = [
    [
        'Unable to access billing portal',
        'Customer portal · 8 min ago',
        'Waiting',
        Clock3,
        'text-amber-600',
    ],
    [
        'Export completed but file is empty',
        'Reports · 24 min ago',
        'In progress',
        MessageSquare,
        'text-sky-600',
    ],
    [
        'Update team member permissions',
        'Account access · 42 min ago',
        'Resolved',
        CheckCircle2,
        'text-emerald-600',
    ],
] as const;

export default function Welcome() {
    const { auth } = usePage().props;

    return (
        <>
            <Head title="Support, kept clear" />
            <div className="min-h-dvh overflow-hidden bg-background text-foreground">
                <header className="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6 lg:px-8">
                    <Link
                        href="/"
                        className="flex items-center gap-3"
                        aria-label="Helpdesk home"
                    >
                        <span className="flex size-9 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-sm">
                            <AppLogoIcon className="size-5 fill-current" />
                        </span>
                        <span className="text-sm font-semibold tracking-tight">
                            Helpdesk
                        </span>
                    </Link>
                    <nav
                        className="flex items-center gap-2 text-sm"
                        aria-label="Main navigation"
                    >
                        {auth.user ? (
                            <Link
                                href={dashboard()}
                                className="rounded-lg bg-primary px-4 py-2 font-medium text-primary-foreground transition-transform hover:-translate-y-0.5 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 active:translate-y-0"
                            >
                                Open dashboard
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={login()}
                                    className="rounded-lg px-4 py-2 font-medium text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                    Sign in
                                </Link>
                                <Link
                                    href={register()}
                                    className="rounded-lg bg-primary px-4 py-2 font-medium text-primary-foreground transition-transform hover:-translate-y-0.5 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 active:translate-y-0"
                                >
                                    Create account
                                </Link>
                            </>
                        )}
                    </nav>
                </header>

                <main>
                    <section className="mx-auto grid w-full max-w-6xl items-center gap-14 px-6 pt-16 pb-20 lg:grid-cols-[0.9fr_1.1fr] lg:px-8 lg:pt-24 lg:pb-28">
                        <div className="max-w-xl">
                            <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-border bg-card px-3 py-1.5 text-xs font-medium text-muted-foreground shadow-sm">
                                <span className="size-1.5 rounded-full bg-amber-500" />
                                Support operations, without the noise
                            </div>
                            <h1 className="max-w-lg text-5xl leading-[1.05] font-semibold tracking-[-0.045em] text-balance sm:text-6xl">
                                Every request, in one clear place.
                            </h1>
                            <p className="mt-6 max-w-lg text-lg leading-8 text-muted-foreground">
                                Helpdesk gives your customers a simple way to
                                ask for help and your team a focused queue to
                                resolve it.
                            </p>
                            <div className="mt-8 flex flex-wrap items-center gap-3">
                                <Link
                                    href={auth.user ? dashboard() : register()}
                                    className="group inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-3 text-sm font-semibold text-primary-foreground shadow-sm transition-transform hover:-translate-y-0.5 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 active:translate-y-0"
                                >
                                    {auth.user
                                        ? 'Open your queue'
                                        : 'Start with Helpdesk'}
                                    <ArrowUpRight className="size-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                                </Link>
                                <Link
                                    href={login()}
                                    className="inline-flex items-center gap-2 rounded-lg border border-border bg-background px-5 py-3 text-sm font-semibold transition-colors hover:bg-muted focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                    Sign in
                                </Link>
                            </div>
                            <div className="mt-10 flex flex-wrap gap-x-6 gap-y-3 text-sm text-muted-foreground">
                                <span className="inline-flex items-center gap-2">
                                    <ShieldCheck className="size-4 text-emerald-600" />
                                    Role-aware access
                                </span>
                                <span className="inline-flex items-center gap-2">
                                    <MessageSquare className="size-4 text-sky-600" />
                                    Clear conversations
                                </span>
                            </div>
                        </div>

                        <div className="relative">
                            <div className="absolute -inset-10 -z-10 rounded-full bg-amber-500/10 blur-3xl" />
                            <div className="overflow-hidden rounded-2xl border border-border bg-card shadow-xl shadow-foreground/5">
                                <div className="flex items-center justify-between border-b border-border px-5 py-4">
                                    <div>
                                        <p className="text-sm font-semibold">
                                            Today&apos;s queue
                                        </p>
                                        <p className="mt-1 text-xs text-muted-foreground">
                                            3 conversations need attention
                                        </p>
                                    </div>
                                    <span className="flex size-9 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600">
                                        <Inbox className="size-4" />
                                    </span>
                                </div>
                                <div className="divide-y divide-border">
                                    {queueItems.map(
                                        ([title, meta, status, Icon, tone]) => (
                                            <article
                                                key={title}
                                                className="flex gap-4 px-5 py-5 transition-colors hover:bg-muted/50"
                                            >
                                                <span
                                                    className={`mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted ${tone}`}
                                                >
                                                    <Icon className="size-4" />
                                                </span>
                                                <div className="min-w-0 flex-1">
                                                    <p className="truncate text-sm font-medium">
                                                        {title}
                                                    </p>
                                                    <p className="mt-1 text-xs text-muted-foreground">
                                                        {meta}
                                                    </p>
                                                </div>
                                                <span className="shrink-0 self-center rounded-md bg-muted px-2 py-1 text-[11px] font-medium text-muted-foreground">
                                                    {status}
                                                </span>
                                            </article>
                                        ),
                                    )}
                                </div>
                                <div className="flex items-center justify-between border-t border-border bg-muted/30 px-5 py-4">
                                    <div className="flex items-center gap-2 text-xs text-muted-foreground">
                                        <LifeBuoy className="size-4" />
                                        Built for focused teams
                                    </div>
                                    <span className="text-xs font-medium text-foreground">
                                        10 open tickets
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </>
    );
}
