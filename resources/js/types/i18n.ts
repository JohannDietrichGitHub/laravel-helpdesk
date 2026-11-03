export type ShellTranslations = {
    app_name: string;
    dashboard: string;
    tickets: string;
    my_tickets: string;
    assigned_tickets: string;
    all_tickets: string;
    new_ticket: string;
    settings: string;
    logout: string;
    language: string;
    save: string;
    cancel: string;
    empty: string;
    flash: { saved: string };
};

export type SharedTranslations = {
    shell: ShellTranslations;
    validation: Record<string, string | Record<string, string>>;
};
