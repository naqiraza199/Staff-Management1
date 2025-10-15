<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Event;
use Filament\Tables\Columns\TextColumn;
use Carbon\Carbon;
use App\Models\Client;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response; 

class EventDetail extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-s-document-text';
    protected static ?string $navigationGroup = 'Reports';
    protected static string $view = 'filament.pages.event-detail';

    public function table(Table $table): Table
    {
        

            $formatClientData = function ($record, $type) {
                    $shift = $record->shift;
                    $clientName = 'N/A';
                    $clientId = null;
                    
                    if ($shift && !empty($shift->client_section)) {
                        $data = is_array($shift->client_section) 
                            ? $shift->client_section 
                            : json_decode($shift->client_section, true);

                        if (!empty($data['client_id'])) {
                            $clientId = $data['client_id'];
                        }
                    }

                    if ($clientId) {
                        $client = \App\Models\Client::find($clientId); 
                        $clientName = $client?->display_name ?? 'Unknown Client';
                    }

                    if ($type === 'client_name') {
                        return $clientName;
                    }

                    if ($type === 'title') {
                        $start = Carbon::parse($record->created_at)->format('d/m/Y h:i a');
                        $end = Carbon::parse($record->created_at)->addHours(6)->format('h:i a'); 

                        return "{$record->title} for {$clientName} @ {$start} - {$end}";
                    }

                     if ($type === 'body') {

                        return $record->body;

                    }

                    return '';
                };
            $companyId = Auth::user()->company->id;
        return $table
        
            ->query(
                 Event::query()
                ->where('from', 'Note')
                ->whereHas('shift', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
            )
            ->columns([
                


           TextColumn::make('client_name')
                    ->label('Client')
                    ->formatStateUsing(fn($state) => "<span style='font-size:13px'>{$state}</span>")
                    ->html()
                    ->getStateUsing(function ($record) {
                        $shift = $record->shift;

                        if (!$shift || empty($shift->client_section)) {
                            return 'N/A';
                        }

                        $data = is_array($shift->client_section)
                            ? $shift->client_section
                            : json_decode($shift->client_section, true);

                        if (empty($data['client_id'])) {
                            return 'N/A';
                        }

                        // Handle both single and multiple client IDs
                        $clientIds = is_array($data['client_id']) ? $data['client_id'] : [$data['client_id']];
                        $clients = Client::whereIn('id', $clientIds)->pluck('display_name')->toArray();

                        return !empty($clients) ? implode(', ', $clients) : 'N/A';
                    })
                    ->sortable(),

                    TextColumn::make('created_at')
                        ->label('Created At')
                        ->formatStateUsing(function ($state) {
                            if (!$state) return '-';
                            
                            $formattedDate = Carbon::parse($state)->format('d-m-Y');
                            
                            return "<span style='font-size:13px'>{$formattedDate}</span>";
                        })
                        ->html() 
                        ->sortable()
                        ->searchable(),

                TextColumn::make('from')
                    ->label('Category')
                    ->searchable()
                    ->formatStateUsing(fn($state) => "<span style='font-size:13px'>{$state}</span>")
                    ->html()
                    ->sortable(),




                TextColumn::make('title')
                        ->label('Summary')
                        ->html()
                        ->getStateUsing(function ($record) {
                            $shift = $record->shift;
                            $clientName = 'Unknown Client';

                            if ($shift && !empty($shift->client_section)) {
                                $data = is_array($shift->client_section)
                                    ? $shift->client_section
                                    : json_decode($shift->client_section, true);

                                if (!empty($data['client_id'])) {
                                    $clientIds = is_array($data['client_id'])
                                        ? $data['client_id']
                                        : [$data['client_id']];

                                    $clients = Client::whereIn('id', $clientIds)
                                        ->pluck('display_name')
                                        ->toArray();

                                    $clientName = implode(', ', $clients);
                                }
                            }

                            $start = Carbon::parse($record->created_at)->format('d/m/Y h:i a');
                            $end   = Carbon::parse($record->created_at)->addHours(6)->format('h:i a');

                            return "{$record->title} for {$clientName} @ {$start} - {$end}";
                        })
                        ->formatStateUsing(fn ($state) =>
                            "<span style='font-size:11px'>" . e(Str::limit(strip_tags($state), 30)) . "</span>"
                        )
                        ->tooltip(function ($record) {
                            $shift = $record->shift;
                            $clientName = 'Unknown Client';

                            if ($shift && !empty($shift->client_section)) {
                                $data = is_array($shift->client_section)
                                    ? $shift->client_section
                                    : json_decode($shift->client_section, true);

                                if (!empty($data['client_id'])) {
                                    $clientIds = is_array($data['client_id'])
                                        ? $data['client_id']
                                        : [$data['client_id']];

                                    $clients = Client::whereIn('id', $clientIds)
                                        ->pluck('display_name')
                                        ->toArray();

                                    $clientName = implode(', ', $clients);
                                }
                            }

                            $start = Carbon::parse($record->created_at)->format('d/m/Y h:i a');
                            $end   = Carbon::parse($record->created_at)->addHours(6)->format('h:i a');

                            return strip_tags("{$record->title} for {$clientName} @ {$start} - {$end}");
                        })
                        ->wrap()
                        ->sortable()
                        ->searchable(),

                     TextColumn::make('body')
                            ->label('Message')
                            ->formatStateUsing(fn($state) => "<span style='font-size:11px'>{$state}</span>")
                            ->html()
                            ->getStateUsing(fn ($record) => \Illuminate\Support\Str::limit(strip_tags($record->body), 20))
                            ->tooltip(fn ($record) => strip_tags($record->body)) 
                            ->wrap()
                            ->sortable()
                            ->searchable(),



                        TextColumn::make('note_attachments')
                            ->label('Attachments')
                            ->tooltip('This note has attachments click it') 
                            ->formatStateUsing(fn () => '📃') 
                            ->extraAttributes(['class' => 'cursor-pointer text-primary-600'])
                            ->action(
                                Action::make('viewAttachments')
                                    ->label('View Attachments')
                                    ->modalHeading('Attachments')
                                    ->modalButton('Close')
                                    ->modalWidth('2xl')
                                    ->modalContent(function ($record) {
                                        $attachments = is_array($record->note_attachments)
                                            ? $record->note_attachments
                                            : json_decode($record->note_attachments, true);

                                        if (empty($attachments)) {
                                            return view('components.no-attachments'); 
                                        }

                                        return view('components.attachments-modal', [
                                            'attachments' => $attachments,
                                        ]);
                                    })
                            ),


            ]) 
            
             ->headerActions([
                        Action::make('DownloadNotes')
                                    ->label('') 
                                    ->icon('heroicon-s-cloud-arrow-down')
                                    ->color('success') 
                                    ->tooltip('Download Event Notes Report')
                                    ->action(function () use ($formatClientData, $table) {
                                        $records = $table->getRecords(); 
                                        
                                        if ($records->isEmpty()) {
                                            $this->notify('warning', 'No records found to export.');
                                            return;
                                        }

                                        $headers = [ 'Client', 'Created At', 'Category', 'Summary', 'Message', 'Has Attachments' ];
                                        $filename = 'event_notes_report_' . now()->format('Ymd_His') . '.csv';

                                        $callback = function () use ($records, $headers, $formatClientData) {
                                            $file = fopen('php://output', 'w');
                                            fputcsv($file, $headers);

                                            foreach ($records as $record) {
                                                
                                                $attachments = is_string($record->note_attachments) 
                                                    ? @json_decode($record->note_attachments, true) 
                                                    : (is_array($record->note_attachments) ? $record->note_attachments : []);

                                                $row = [
                                                    $formatClientData($record, 'client_name'),
                                                    
                                                    $record->created_at ? Carbon::parse($record->created_at)->format('d-m-Y') : '-',
                                                    
                                                    $record->from,
                                                    
                                                    $formatClientData($record, 'title'),
                                                    
                                                    $record->body,
                                                    
                                                    empty($attachments) ? 'No' : 'Yes',
                                                ];
                                                fputcsv($file, $row);
                                            }
                                            fclose($file);
                                        };
                                         return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
                                    }),


            ])
            ->filters([
            Tables\Filters\Filter::make('date_range')
                ->form([
                        DatePicker::make('start_date')
                        ->label('From')
                        ->closeOnDateSelection(),
                        DatePicker::make('end_date')
                        ->label('To')
                        ->closeOnDateSelection(),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['start_date'] && $data['end_date'],
                        fn ($query) => $query->whereBetween('created_at', [$data['start_date'], $data['end_date']])
                    );
                }),
            ]);
    }
}
