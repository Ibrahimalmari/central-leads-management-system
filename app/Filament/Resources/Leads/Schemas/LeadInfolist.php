<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Support\LeadFieldFormatter;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.lead_summary'))
                    ->icon(Heroicon::OutlinedInboxStack)
                    ->columnSpanFull()
                    ->extraAttributes([
                        'class' => 'cl-lead-section cl-lead-section--summary',
                    ])
                    ->columns([
                        'md' => 2,
                        'xl' => 4,
                    ])
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('admin.fields.submitted_at'))
                            ->dateTime(),
                        TextEntry::make('status')
                            ->label(__('admin.fields.status'))
                            ->formatStateUsing(fn (string $state): string => __("admin.statuses.{$state}"))
                            ->badge(),
                        TextEntry::make('company.name')
                            ->label(__('admin.fields.company'))
                            ->placeholder('-'),
                        TextEntry::make('site.name')
                            ->label(__('admin.fields.site'))
                            ->placeholder('-'),
                        TextEntry::make('form.name')
                            ->label(__('admin.fields.known_form'))
                            ->placeholder('-'),
                        TextEntry::make('form_key')
                            ->label(__('admin.fields.form_key'))
                            ->placeholder('-')
                            ->copyable(),
                        TextEntry::make('form_name')
                            ->label(__('admin.fields.form'))
                            ->placeholder('-'),
                        TextEntry::make('form_type')
                            ->label(__('admin.fields.type'))
                            ->placeholder('-'),
                    ]),

                Grid::make([
                    'lg' => 3,
                ])
                    ->columnSpanFull()
                    ->extraAttributes([
                        'class' => 'cl-lead-details-grid',
                    ])
                    ->schema([
                        Section::make(__('admin.sections.contact_details'))
                            ->icon(Heroicon::OutlinedUser)
                            ->columnSpan([
                                'default' => 'full',
                                'lg' => 2,
                            ])
                            ->extraAttributes([
                                'class' => 'cl-lead-section cl-lead-section--contact',
                            ])
                            ->columns([
                                'md' => 2,
                            ])
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('admin.fields.name'))
                                    ->placeholder('-'),
                                TextEntry::make('email')
                                    ->label(__('admin.fields.email'))
                                    ->placeholder('-')
                                    ->copyable()
                                    ->url(fn ($state) => filled($state) ? "mailto:{$state}" : null),
                                TextEntry::make('phone')
                                    ->label(__('admin.fields.phone'))
                                    ->placeholder('-')
                                    ->copyable()
                                    ->url(fn ($state) => filled($state) ? "tel:{$state}" : null),
                                TextEntry::make('page_url')
                                    ->label(__('admin.fields.page'))
                                    ->placeholder('-')
                                    ->copyable()
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab()
                                    ->columnSpanFull(),
                            ]),

                        Section::make(__('admin.sections.follow_up'))
                            ->icon(Heroicon::OutlinedShieldCheck)
                            ->columnSpan([
                                'default' => 'full',
                                'lg' => 1,
                            ])
                            ->extraAttributes([
                                'class' => 'cl-lead-section cl-lead-section--follow',
                            ])
                            ->schema([
                                TextEntry::make('assignee.name')
                                    ->label(__('admin.fields.assigned_to'))
                                    ->placeholder(__('admin.fields.unassigned')),
                                TextEntry::make('assigned_at')
                                    ->label(__('admin.fields.assigned_at'))
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('last_contacted_at')
                                    ->label(__('admin.fields.last_contacted_at'))
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make(__('admin.fields.message'))
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->columnSpanFull()
                    ->extraAttributes([
                        'class' => 'cl-lead-section cl-lead-section--message',
                    ])
                    ->visible(fn ($record): bool => filled($record->message))
                    ->schema([
                        TextEntry::make('message')
                            ->hiddenLabel()
                            ->placeholder('-')
                            ->columnSpanFull()
                            ->extraEntryWrapperAttributes([
                                'class' => 'cl-lead-message',
                            ]),
                    ]),

                Section::make(__('admin.fields.additional_data'))
                    ->icon(Heroicon::OutlinedSquaresPlus)
                    ->columnSpanFull()
                    ->extraAttributes([
                        'class' => 'cl-lead-section cl-lead-section--extra',
                    ])
                    ->description(__('admin.messages.extra_data_description'))
                    ->schema([
                        ViewEntry::make('raw_data')
                            ->hiddenLabel()
                            ->view('filament.leads.extra-data')
                            ->viewData(fn ($record): array => [
                                'items' => LeadFieldFormatter::extraDataItems($record->raw_data),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make(__('admin.sections.technical_details'))
                    ->icon(Heroicon::OutlinedCommandLine)
                    ->columnSpanFull()
                    ->extraAttributes([
                        'class' => 'cl-lead-section cl-lead-section--technical',
                    ])
                    ->collapsed()
                    ->columns([
                        'md' => 2,
                    ])
                    ->schema([
                        TextEntry::make('ip_address')
                            ->label(__('admin.fields.ip_address'))
                            ->placeholder('-')
                            ->copyable(),
                        TextEntry::make('user_agent')
                            ->label(__('admin.fields.user_agent'))
                            ->placeholder('-')
                            ->copyable()
                            ->columnSpanFull(),
                        TextEntry::make('updated_at')
                            ->label(__('admin.fields.updated_at'))
                            ->dateTime(),
                    ]),
            ]);
    }
}
