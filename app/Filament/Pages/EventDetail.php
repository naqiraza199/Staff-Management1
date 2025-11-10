<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\Shift;
use App\Models\ShiftNote;
use App\Models\Company;
use Filament\Facades\Filament;

class EventDetail extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-s-document-text';
    protected static ?string $navigationGroup = 'Reports';
    protected static string $view = 'filament.pages.event-detail';

    // Optional filter state (Filament Table filters will populate these via query closures)
    public ?string $noteType = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
                             public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('manage-event-details');
        }
    /**
     * Helper: extract client names from a shift (client_section JSON) or from client_id
     * Returns string: single or multiple client display_names joined by comma, or "N/A"
     */
    protected function resolveClientNameFromRecord($record): string
    {
        // Prefer client_id (direct on ShiftNote) if present
        if (!empty($record->client_id)) {
            $client = Client::find($record->client_id);
            if ($client) {
                return $client->display_name;
            }
        }

        // Fallback: try to read from shift->client_section JSON
        $shift = $record->shift;
        if ($shift && !empty($shift->client_section)) {
            $data = is_array($shift->client_section) ? $shift->client_section : @json_decode($shift->client_section, true);
            if (!empty($data['client_id'])) {
                $clientIds = is_array($data['client_id']) ? $data['client_id'] : [$data['client_id']];
                $names = Client::whereIn('id', $clientIds)->pluck('display_name')->toArray();
                if (!empty($names)) {
                    return implode(', ', $names);
                }
            }

            // older code placed client info in client_details (safety)
            if (!empty($data['client_details']) && is_array($data['client_details'])) {
                $names = collect($data['client_details'])->pluck('client_name')->filter()->unique()->values()->toArray();
                if (!empty($names)) {
                    return implode(', ', $names);
                }
            }
        }

        return 'N/A';
    }

    /**
     * Build Filament Table
     */
   public function table(Table $table): Table
{
    $authUser = Auth::user();
    $companyId = Company::where('user_id', $authUser->id)->value('id');

    
    return $table
        ->query(function () use ($authUser, $companyId) {
    $notes = ShiftNote::query()
        ->with('shift')
        ->where(function ($q) use ($authUser, $companyId) {
            $q->whereHas('shift', fn($s) => $s->where('company_id', $companyId))
              ->orWhereIn('client_id', Client::where('user_id', $authUser->id)->pluck('id'));
        })
        ->latest()
        ->get();

    // Expand notes having multiple clients
    $expanded = collect();

    foreach ($notes as $note) {
        $clients = [];

        // Case 1: direct client_id
        if (!empty($note->client_id)) {
            $clients[] = $note->client_id;
        }

        // Case 2: shift->client_section JSON
        $shift = $note->shift;
        if ($shift && !empty($shift->client_section)) {
            $data = is_array($shift->client_section)
                ? $shift->client_section
                : @json_decode($shift->client_section, true);

            if (isset($data['client_id'])) {
                $clientIds = is_array($data['client_id']) ? $data['client_id'] : [$data['client_id']];
                $clients = array_merge($clients, $clientIds);
            } elseif (is_array($data)) {
                foreach ($data as $item) {
                    if (!empty($item['client_id'])) {
                        $clients[] = $item['client_id'];
                    }
                }
            }
        }

        $clients = array_unique(array_filter($clients));

        // Duplicate record per client
        if (empty($clients)) {
            $expanded->push($note);
        } else {
            foreach ($clients as $clientId) {
                $clone = clone $note;
                $clone->resolved_client_id = $clientId;
                $expanded->push($clone);
            }
        }
    }

    // Convert collection back to query-like builder for Filament
    return \App\Models\ShiftNote::query()->whereIn('id', $expanded->pluck('id')->unique());
})
        ->columns([
            TextColumn::make('client')
                ->label('Client')
                ->getStateUsing(function ($record) {
                    $clientNames = [];

                    // If direct client_id exists
                    if (!empty($record->client_id)) {
                        $client = Client::find($record->client_id);
                        if ($client) {
                            $clientNames[] = $client->display_name ?? $client->full_name ?? 'N/A';
                        }
                    }

                    // Otherwise, check shift->client_section JSON
                    $shift = $record->shift;
                    if ($shift && !empty($shift->client_section)) {
                        $data = is_array($shift->client_section)
                            ? $shift->client_section
                            : @json_decode($shift->client_section, true);

                        // Normalize both array and single-client JSON formats
                        if (isset($data['client_id'])) {
                            $clientIds = is_array($data['client_id'])
                                ? $data['client_id']
                                : [$data['client_id']];
                            $clientNames = array_merge(
                                $clientNames,
                                Client::whereIn('id', $clientIds)->pluck('display_name')->toArray()
                            );
                        } elseif (is_array($data)) {
                            // Handle array of multiple client entries
                            foreach ($data as $item) {
                                if (!empty($item['client_id'])) {
                                    $client = Client::find($item['client_id']);
                                    if ($client) {
                                        $clientNames[] = $client->display_name ?? $client->full_name ?? 'N/A';
                                    }
                                }
                            }
                        }
                    }

                    // ⚙️ Split clients — only show the FIRST one per row in table
                    // For multiple clients in one shift, we'll duplicate rows below
                    return implode(', ', array_unique($clientNames)) ?: 'N/A';
                })
                ->html()
                ->sortable()
                ->searchable(),

            TextColumn::make('created_at')
                ->label('Created At')
                ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d-m-Y') : '-')
                ->sortable()
                ->searchable(),

            TextColumn::make('note_type')
                ->label('Category')
                ->formatStateUsing(fn ($state) => "<span style='font-size:13px'>" . e($state ?? '-') . "</span>")
                ->html()
                ->sortable()
                ->searchable(),

            TextColumn::make('title')
                ->label('Summary')
                ->getStateUsing(function ($record) {
                    $clientName = $this->resolveClientNameFromRecord($record);
                    $start = $record->created_at ? Carbon::parse($record->created_at)->format('d/m/Y h:i a') : '-';
                    return "{$record->title} for {$clientName} @ {$start}";
                })
                ->formatStateUsing(fn ($state) => "<span style='font-size:11px'>" . e(Str::limit(strip_tags($state), 30)) . "</span>")
                ->tooltip(fn ($record) => strip_tags($record->title ?? ''))
                ->html()
                ->wrap()
                ->sortable()
                ->searchable(),

            TextColumn::make('note_body')
                ->label('Message')
                ->formatStateUsing(fn ($state) => "<span style='font-size:11px'>" . e(Str::limit(strip_tags($state ?? ''), 40)) . "</span>")
                ->tooltip(fn ($record) => strip_tags($record->note_body ?? ''))
                ->html()
                ->wrap()
                ->sortable()
                ->searchable(),

            
        ])
        ->actions([
        Tables\Actions\Action::make('viewAttachments')
        ->label('View Attachments')
        ->icon('heroicon-o-paper-clip')
        ->color('success')
        ->visible(fn ($record) => !empty($record->attachments)) // 👈 only show if attachments exist
        ->modalHeading('Attachments')
        ->modalButton('Close')
        ->modalWidth('2xl')
        ->modalContent(function ($record) {
            $attachments = is_string($record->attachments)
                ? @json_decode($record->attachments, true)
                : (is_array($record->attachments) ? $record->attachments : []);

            return view('components.attachments-modal', [
                'attachments' => $attachments,
            ]);
        }),

    Tables\Actions\Action::make('noAttachments')
        ->label('No Attachments')
        ->icon('heroicon-o-x-circle')
        ->color('gray')
        ->visible(fn ($record) => empty($record->attachments)) // 👈 only show if empty
        ->disabled()
])
 ->headerActions([ 
               Tables\Actions\Action::make('clients')
                    ->label('Clients')
                    ->icon('heroicon-s-users')
                    ->color('info')
                    ->url(fn () => url('/admin/event-detail')),

                Tables\Actions\Action::make('staffs')
                    ->label('Staffs')
                    ->outlined()
                    ->icon('heroicon-s-users')
                    ->color('warning')
                    ->url(fn () => url('/admin/event-detail-staff')),


                ])

        ->filters([
            Tables\Filters\SelectFilter::make('note_type')
                ->label('Note Type')
                ->options(
                    ShiftNote::query()->distinct()->pluck('note_type', 'note_type')->filter()->toArray()
                )
                ->placeholder('All Types'),

            Tables\Filters\Filter::make('date_range')
                ->form([
                    DatePicker::make('start_date')->label('From')->closeOnDateSelection(),
                    DatePicker::make('end_date')->label('To')->closeOnDateSelection(),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['start_date'] && $data['end_date'],
                        fn ($query) => $query->whereBetween('created_at', [
                            Carbon::parse($data['start_date'])->startOfDay(),
                            Carbon::parse($data['end_date'])->endOfDay(),
                        ])
                    );
                }),
        ])
        ->defaultSort('created_at', 'desc')
        ->recordUrl(fn ($record) => url("/admin/client-communication?client_id={$record->client_id}"));


}

}
