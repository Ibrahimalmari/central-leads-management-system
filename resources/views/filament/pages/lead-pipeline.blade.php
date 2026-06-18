<x-filament-panels::page>
    <div class="cl-pipeline">
        @foreach ($this->getStatusColumns() as $column)
            <section class="cl-pipeline__column">
                <header class="cl-pipeline__header">
                    <span>{{ $column['label'] }}</span>
                    <strong>{{ $column['count'] }}</strong>
                </header>

                <div class="cl-pipeline__items">
                    @forelse ($column['leads'] as $lead)
                        <a class="cl-pipeline__item" href="{{ $this->leadUrl($lead) }}">
                            <div class="cl-pipeline__item-top">
                                <strong>{{ $lead->name ?: $lead->phone ?: $lead->email ?: __('admin.models.lead').' #'.$lead->id }}</strong>
                                <span>{{ $lead->created_at?->diffForHumans() }}</span>
                            </div>
                            <p>{{ $lead->message ? str($lead->message)->limit(90) : ($lead->form_name ?: $lead->form_key ?: '-') }}</p>
                            <footer>
                                <span>{{ $lead->site?->name ?: '-' }}</span>
                                <span>{{ $lead->assignee?->name ?: __('admin.fields.unassigned') }}</span>
                            </footer>
                        </a>
                    @empty
                        <div class="cl-pipeline__empty">{{ __('admin.stats.no_leads_yet') }}</div>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>

    <style>
        .cl-pipeline {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(18rem, 1fr);
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: .5rem;
        }

        .cl-pipeline__column {
            min-height: 26rem;
            border: 1px solid rgba(15, 23, 42, .10);
            border-radius: 8px;
            background: rgba(248, 250, 252, .86);
        }

        .cl-pipeline__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(15, 23, 42, .10);
            padding: .85rem 1rem;
            font-weight: 700;
        }

        .cl-pipeline__header strong {
            min-width: 2rem;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            padding: .15rem .55rem;
            text-align: center;
            font-size: .8rem;
        }

        .cl-pipeline__items {
            display: grid;
            gap: .75rem;
            padding: .85rem;
        }

        .cl-pipeline__item,
        .cl-pipeline__empty {
            border: 1px solid rgba(15, 23, 42, .10);
            border-radius: 8px;
            background: #fff;
            padding: .85rem;
        }

        .cl-pipeline__item {
            display: grid;
            gap: .55rem;
            text-decoration: none;
            transition: transform .15s ease, border-color .15s ease;
        }

        .cl-pipeline__item:hover {
            border-color: #2563eb;
            transform: translateY(-1px);
        }

        .cl-pipeline__item-top,
        .cl-pipeline__item footer {
            display: flex;
            gap: .75rem;
            justify-content: space-between;
        }

        .cl-pipeline__item-top strong {
            color: #0f172a;
            font-size: .95rem;
        }

        .cl-pipeline__item-top span,
        .cl-pipeline__item footer,
        .cl-pipeline__item p,
        .cl-pipeline__empty {
            color: #64748b;
            font-size: .82rem;
            line-height: 1.7;
        }

        .dark .cl-pipeline__column {
            border-color: rgba(148, 163, 184, .18);
            background: rgba(15, 23, 42, .72);
        }

        .dark .cl-pipeline__header,
        .dark .cl-pipeline__item,
        .dark .cl-pipeline__empty {
            border-color: rgba(148, 163, 184, .18);
        }

        .dark .cl-pipeline__item,
        .dark .cl-pipeline__empty {
            background: rgba(2, 6, 23, .55);
        }

        .dark .cl-pipeline__item-top strong {
            color: #f8fafc;
        }
    </style>
</x-filament-panels::page>
