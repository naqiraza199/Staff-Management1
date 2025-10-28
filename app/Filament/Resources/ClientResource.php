<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\AdditionalContact;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use App\Models\StaffProfile;
use Filament\Infolists;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Grid;
use Carbon\Carbon;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Forms\Components\CheckboxList;
use App\Models\PriceBook;
use App\Models\ClientHasPriceBook;
use Filament\Forms\Components\View;
use Filament\Infolists\Components\View as InfolistView;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\ViewField;
use App\Models\Team;
use App\Models\ClientType;
use Filament\Forms\Components\Textarea;


class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationGroup = 'Client Management';

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?int $navigationSort = 2;


    public static function getModelLabel(): string
    {
        return 'Client';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Clients';
    }

    public static function getEloquentQuery(): Builder
    {
        $authUser = Auth::user();

        return Client::where('user_id', $authUser->id)->where('is_archive', 'Unarchive');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Client Detail')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 5])
                            ->schema([
                                Forms\Components\Fieldset::make('Salutation')
                                    ->schema([
                                        Forms\Components\Checkbox::make('use_salutation')
                                            ->label('Use salutation')
                                            ->inline()
                                            ->default(true)
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Forms\Components\Select::make('salutation')
                                            ->options([
                                                'Mr' => 'Mr',
                                                'Mrs' => 'Mrs',
                                                'Miss' => 'Miss',
                                                'Ms' => 'Ms',
                                                'Mx' => 'Mx',
                                                'Doctor' => 'Doctor',
                                                'Them' => 'Them',
                                                'They' => 'They',
                                            ])
                                            ->placeholder('Select')
                                            ->disabled(fn($get) => !$get('use_salutation')),
                                    ])
                                    ->columnSpan(2),
                                Forms\Components\Fieldset::make('Client Info')
                                    ->schema([
                                        Forms\Components\TextInput::make('first_name')->label('First name')->placeholder('Enter First Name')->required(),
                                        Forms\Components\TextInput::make('middle_name')->label('Middle name')->placeholder('Enter Middle Name'),
                                        Forms\Components\TextInput::make('last_name')->label('Last name')->placeholder('Enter Last/Family Name')->required(),
                                        Forms\Components\TextInput::make('email')->email()->label('Email Address')->placeholder('Enter Email')->required(),
                                    ])
                                    ->columnSpan(3)
                                    ->columns(2),
                            ]),
                        Forms\Components\Fieldset::make('Display Name')
                            ->schema([
                                Forms\Components\TextInput::make('display_name')->label('')->placeholder('Enter Display Name')->columnSpanFull(),
                            ]),
                        Forms\Components\Fieldset::make('Contact')
                            ->schema([
                                Forms\Components\Grid::make(['default' => 2])
                                    ->schema([
                                        Forms\Components\TextInput::make('mobile_number')->label('Mobile Number')->placeholder('Enter Mobile Number')->columnSpan(1),
                                        Forms\Components\TextInput::make('phone_number')->placeholder('Phone Number')->columnSpan(1),
                                    ]),
                            ]),
                        Forms\Components\Fieldset::make('Personal Information')
                            ->schema([
                                Forms\Components\Grid::make(['default' => 3])
                                    ->schema([
                                        Forms\Components\Select::make('gender')->options([
                                            'Male' => 'Male',
                                            'Female' => 'Female',
                                            'Other' => 'Other',
                                        ])->columnSpan(1),
                                        Forms\Components\DatePicker::make('dob')->label('Date Of Birth')->columnSpan(1),
                                        Forms\Components\Select::make('marital_status')->options([
                                            'Single' => 'Single',
                                            'Married' => 'Married',
                                            'De Facto' => 'De Facto',
                                            'Divorced' => 'Divorced',
                                            'Separated' => 'Separated',
                                            'Widowed' => 'Widowed',
                                        ])->columnSpan(1),
                                    ]),
                            ]),
                        Forms\Components\Fieldset::make('Additional Contacts')
                            ->schema([
                                Forms\Components\Grid::make(['default' => 3])
                                    ->schema([
                                        Forms\Components\TextInput::make('religion')->placeholder('Enter Religion')->columnSpan(1),
                                        Forms\Components\TextInput::make('nationality')->placeholder('Enter Nationality')->columnSpan(1),
                                        Forms\Components\TextInput::make('unit_or_appartment_no')->placeholder('Enter Unit/Appartment Number')->columnSpan(1),
                                    ]),
                                Forms\Components\Select::make('languages')
                                    ->label('Languages')
                                    ->multiple()
                                    ->searchable()
                                    ->options([
                                        '!O!ung' => '!O!ung',
                                        '!Xóõ' => '!Xóõ',
                                        "'Are'are" => "'Are'are",
                                        "'Auhelawa" => "'Auhelawa",
                                        "A'tong" => "A'tong",
                                        'A-Pucikwar' => 'A-Pucikwar',
                                        'Aari' => 'Aari',
                                        'Aasáx' => 'Aasáx',
                                        'Abadi' => 'Abadi',
                                        'Abaga' => 'Abaga',
                                        'Abai Sungai' => 'Abai Sungai',
                                        'Abanyom' => 'Abanyom',
                                        'Abar' => 'Abar',
                                        'Abau' => 'Abau',
                                        'Abaza' => 'Abaza',
                                        'Abellen Ayta' => 'Abellen Ayta',
                                        'Abidji' => 'Abidji',
                                        'Abinomn' => 'Abinomn',
                                        'Abkhazian' => 'Abkhazian',
                                        'Abom' => 'Abom',
                                        'Abon' => 'Abon',
                                        'Abron' => 'Abron',
                                        'Abu' => 'Abu',
                                        "Abu' Arapesh" => "Abu' Arapesh",
                                        'Abua' => 'Abua',
                                        'Abui' => 'Abui',
                                        'Abun' => 'Abun',
                                        'Abure' => 'Abure',
                                        'Abureni' => 'Abureni',
                                        'Abé' => 'Abé',
                                        "Acatepec Me'phaa" => "Acatepec Me'phaa",
                                        'Achagua' => 'Achagua',
                                        'Achang' => 'Achang',
                                        'Ache' => 'Ache',
                                        'Acheron' => 'Acheron',
                                        'Achi' => 'Achi',
                                        'Achinese' => 'Achinese',
                                        'Achterhoeks' => 'Achterhoeks',
                                        'Achuar-Shiwiar' => 'Achuar-Shiwiar',
                                        'Achumawi' => 'Achumawi',
                                        'Aché' => 'Aché',
                                        'Acoli' => 'Acoli',
                                        'Adabe' => 'Adabe',
                                        'Adamawa Fulfulde' => 'Adamawa Fulfulde',
                                        'Adamorobe Sign Language' => 'Adamorobe Sign Language',
                                        'Adang' => 'Adang',
                                        'Adangbe' => 'Adangbe',
                                        'Adangme' => 'Adangme',
                                        'Adap' => 'Adap',
                                        'Adasen' => 'Adasen',
                                        'Adele' => 'Adele',
                                        'Adhola' => 'Adhola',
                                        'Adi' => 'Adi',
                                        'Adioukrou' => 'Adioukrou',
                                        'Adivasi Oriya' => 'Adivasi Oriya',
                                        'Adiwasi Garasia' => 'Adiwasi Garasia',
                                        'Adnyamathanha' => 'Adnyamathanha',
                                        'Adonara' => 'Adonara',
                                        'Aduge' => 'Aduge',
                                        'Adyghe' => 'Adyghe',
                                        'Adzera' => 'Adzera',
                                        'Aeka' => 'Aeka',
                                        'Aekyom' => 'Aekyom',
                                        'Aer' => 'Aer',
                                        'Afade' => 'Afade',
                                        'Afar' => 'Afar',
                                        'Afghan Sign Language' => 'Afghan Sign Language',
                                        'Afitti' => 'Afitti',
                                        'Afrikaans' => 'Afrikaans',
                                        'Afro-Seminole Creole' => 'Afro-Seminole Creole',
                                        'Agarabi' => 'Agarabi',
                                        'Agariya' => 'Agariya',
                                        'Agatu' => 'Agatu',
                                        'Aghem' => 'Aghem',
                                        'Aghu' => 'Aghu',
                                        'Aghul' => 'Aghul',
                                        'Agi' => 'Agi',
                                        'Agob' => 'Agob',
                                        'Agoi' => 'Agoi',
                                        'Aguacateco' => 'Aguacateco',
                                        'Aguaruna' => 'Aguaruna',
                                        'Aguna' => 'Aguna',
                                        'Agusan Manobo' => 'Agusan Manobo',
                                        'Agutaynen' => 'Agutaynen',
                                        'Agwagwune' => 'Agwagwune',
                                        'Ahanta' => 'Ahanta',
                                        'Aheu' => 'Aheu',
                                        'Ahirani' => 'Ahirani',
                                        'Ahtena' => 'Ahtena',
                                        'Ahwai' => 'Ahwai',
                                        'Ai-Cham' => 'Ai-Cham',
                                        'Aighon' => 'Aighon',
                                        'Aikanã' => 'Aikanã',
                                        'Aiklep' => 'Aiklep',
                                        'Aimaq' => 'Aimaq',
                                        'Aimele' => 'Aimele',
                                        'Aimol' => 'Aimol',
                                        'Ainbai' => 'Ainbai',
                                        'Ainu (China)' => 'Ainu (China)',
                                        'Ainu (Japan)' => 'Ainu (Japan)',
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Fieldset::make('Address')
                            ->schema([
                                Textarea::make('address')->label('')->placeholder('Enter Address')->columnSpanFull(),
                            ]),
                        Forms\Components\Fieldset::make('Profile Picture')
                            ->schema([
                                Forms\Components\FileUpload::make('pic')->label('')->placeholder('Profile Picture')->columnSpanFull(),
                            ]),

                          Forms\Components\Fieldset::make('Client Settings')
                            ->schema([
                                Forms\Components\Grid::make(['default' => 3])
                                    ->schema([
                                        Forms\Components\TextInput::make('NDIS_number')->placeholder('Enter NDIS Number')->columnSpan(1),
                                        Forms\Components\TextInput::make('aged_care_recipient_ID')->placeholder('Enter Aged Care Recipient ID')->columnSpan(1),
                                        Forms\Components\TextInput::make('reference_number')->placeholder('Enter Reference Number')->columnSpan(1),
                                        Forms\Components\TextInput::make('custom_field')->placeholder('Enter Custom Field')->columnSpan(1),
                                        Forms\Components\TextInput::make('PO_number')->placeholder('Enter PO Number')->columnSpan(1),
                                        Forms\Components\Select::make('client_type_id')
                                            ->label('Client Type')
                                            ->placeholder('Select Client Type')
                                            ->options(function () {
                                                $user = Auth::user();
                                                $companyId = Company::where('user_id', $user->id)->value('id');

                                                return ClientType::where('company_id', $companyId)
                                                    ->where('status', 'Active')
                                                    ->pluck('name', 'id') // 👈 shows name, saves id
                                                    ->toArray();
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(1),
                                     
                                    ]),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->columns([
                Tables\Columns\TextColumn::make('client_no')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Inactive' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                    ]),
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                        'Other' => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->button()->color('warning')->label('')->iconbutton()->tooltip('View Client'),
                Tables\Actions\EditAction::make()->button()->color('stripe')->label('')->iconbutton()->tooltip('Edit Client'),
                Action::make('Archive')->button()->color('darkk')->icon('heroicon-s-archive-box')->label('')->iconbutton()->tooltip('Move to Archive')
                    ->action(function ($record) {
                        $record->is_archive = 'Archive';
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('Success')
                            ->body('Client Archived Successfully')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(5)
                    ->schema([
                        // Main Client Information Section (2/3 width)
                             Section::make('')
                            ->schema([
                        Section::make('Client Information')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('display_name')
                                            ->label('Display Name')
                                            ->weight('bold')
                                            ->icon('heroicon-m-user')
                                            ->size('lg'),
                                       ImageEntry::make('pic')
                                    ->label('')
                                    ->circular()
                                    ->size(200)
                                    ->visible(fn ($state) => $state !== null),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('mobile_number')
                                            ->label('Mobile Number')
                                            ->icon('heroicon-m-device-phone-mobile'),
                                        TextEntry::make('email')
                                            ->label('Email Address')
                                            ->icon('heroicon-m-envelope'),
                                             TextEntry::make('phone_number')
                                            ->label('Phone Number')
                                            ->icon('heroicon-m-phone'),
                                    ]),
                                TextEntry::make('address')
                                    ->label('Address')
                                    ->icon('heroicon-m-map-pin'),
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('gender')
                                            ->label('Gender')
                                            ->badge()
                                            ->color('info'),
                                        TextEntry::make('dob')
                                            ->label('Date of Birth')
                                            ->date()
                                            ->icon('heroicon-m-calendar'),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('marital_status')
                                            ->label('Marital Status')
                                            ->badge()
                                            ->color('success'),
                                        TextEntry::make('nationality')
                                            ->label('Nationality')
                                            ->badge()
                                            ->color('warning'),
                                    ]),
                                TextEntry::make('languages')
                                    ->label('Languages')
                                    ->icon('heroicon-m-language'),
                            ])->headerActions([
                                InfolistAction::make('edit_client')
                                    ->label('EDIT CLIENT')
                                    ->size('sm')
                                    ->color('primary')
                                    ->url(fn ($record) => ClientResource::getUrl('edit', ['record' => $record]))
                                    ->openUrlInNewTab(false)
                                        
                            ]),

                        Section::make('My Price Books')
                            ->schema([
                                InfolistView::make('partials.price-book-table')
                                    ->columnSpanFull(),
                            ])
                            ->headerActions([


InfolistAction::make('manage_price_book')
    ->label('MANAGE PRICE BOOKS')
    ->modalHeading('Manage Price Books')
    ->size('sm')
    ->modalButton('Assign')
    ->form(function ($record) {
        $companyId = \App\Models\Company::where('user_id', auth()->id())->value('id');
        $priceBooks = PriceBook::where('company_id', $companyId)->get();

        $selected = ClientHasPriceBook::where('client_id', $record->id)->get();
        $defaultId = $selected->firstWhere('is_default', 1)?->price_book_id;



$fields[] = Forms\Components\Section::make('Price Books')
    ->schema(function () use ($priceBooks, $selected, $defaultId) {
        $rows = [];

        foreach ($priceBooks as $book) {
            $rows[] = Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Checkbox::make("selected_price_books.{$book->id}")
                        ->default($selected->pluck('price_book_id')->contains($book->id))
                        ->label(''),

                    Forms\Components\Placeholder::make("price_book_name_{$book->id}")
                        ->label('Price Book')
                        ->content($book->name),

                    Forms\Components\Radio::make('default_price_book')
                        ->options([$book->id => ''])
                        ->default($defaultId)
                        ->inline(false)
                        ->label('Default'),

                    Forms\Components\Placeholder::make("charge_type_{$book->id}")
                        ->label('Charge Type')
                        ->content($book->fixed_price ? 'Fixed Price' : 'Per Hour'),

                    Forms\Components\Placeholder::make("provider_travel_{$book->id}")
                        ->label('Provider Travel')
                        ->content($book->provider_travel ? 'Yes' : 'No'),

                    Forms\Components\Placeholder::make("divider_{$book->id}")
                        ->content(new \Illuminate\Support\HtmlString('<hr class="my-2 border-gray-300">'))
                        ->disableLabel()
                        ->columnSpanFull(),
                ])
                ->columns(5)
                ->columnSpanFull();
        }

        return $rows;
    })
    ->collapsible() // optional: makes the section collapsible
    ->compact();    // optional: makes section look tighter




        return $fields;
    })
    ->action(function (array $data, $record) {
        $clientId = $record->id;

        ClientHasPriceBook::where('client_id', $clientId)->delete();

        if (! empty($data['selected_price_books'])) {
            foreach ($data['selected_price_books'] as $bookId => $checked) {
                if ($checked) {
                    ClientHasPriceBook::create([
                        'client_id'     => $clientId,
                        'price_book_id' => $bookId,
                        'is_default'    => ($data['default_price_book'] == $bookId),
                    ]);
                }
            }
        }

        Notification::make()
            ->title('Assigned successfully') // ✅ Notification title
            ->success()
            ->send();
    })

]),
                          Section::make('General Client Info | Shared Accessed')
                                ->schema([
                                    InfolistView::make('filament.infolists.client-general')
                                        ->columnSpanFull(),
                                ])
                                ->headerActions([
                                    // ✅ Add Action (Visible only if empty)
                                    InfolistAction::make('addGeneralInfo')
                                        ->label('Add')
                                        ->visible(fn ($record) =>
                                            empty($record->need_to_know_information)
                                            && empty($record->useful_information)
                                        )
                                        ->color('primary')
                                        ->icon('heroicon-o-plus')
                                        ->modalHeading('Add General Client Information')
                                        ->modalSubmitActionLabel('Create')
                                        ->modalWidth('xl')
                                        ->form([
                                            Textarea::make('need_to_know_information')
                                                ->label('Need to Know Information')
                                                ->rows(5)
                                                ->placeholder('Enter need to know details...')
                                                ->required(),

                                            Textarea::make('useful_information')
                                                ->label('Useful Information')
                                                ->rows(5)
                                                ->placeholder('Enter useful information...')
                                                ->required(),
                                        ])
                                        ->action(function (array $data, $record): void {
                                            // ✅ Create (update existing record)
                                            $record->update([
                                                'need_to_know_information' => $data['need_to_know_information'],
                                                'useful_information'       => $data['useful_information'],
                                            ]);

                                            Notification::make()
                                                ->title('Client general information added successfully.')
                                                ->success()
                                                ->send();
                                        }),

                                    // ✅ Edit Action (Visible only if data exists)
                                    InfolistAction::make('editGeneralInfo')
                                        ->label('Edit')
                                        ->visible(fn ($record) =>
                                            !empty($record->need_to_know_information)
                                            || !empty($record->useful_information)
                                        )
                                        ->color('primary')
                                        ->icon('heroicon-o-pencil')
                                        ->modalHeading('Edit General Client Information')
                                        ->modalSubmitActionLabel('Update')
                                        ->modalWidth('xl')
                                        ->form([
                                            Textarea::make('need_to_know_information')
                                                ->label('Need to Know Information')
                                                ->rows(5)
                                                ->default(fn ($record) => $record?->need_to_know_information)
                                                ->placeholder('Enter need to know details...'),

                                            Textarea::make('useful_information')
                                                ->label('Useful Information')
                                                ->rows(5)
                                                ->default(fn ($record) => $record?->useful_information)
                                                ->placeholder('Enter useful information...'),
                                        ])
                                        ->action(function (array $data, $record): void {
                                            // ✅ Update existing record
                                            $record->update([
                                                'need_to_know_information' => $data['need_to_know_information'],
                                                'useful_information'       => $data['useful_information'],
                                            ]);

                                            Notification::make()
                                                ->title('Client general information updated successfully.')
                                                ->success()
                                                ->send();
                                        }),
                                ]),
                                Section::make('Additional Information')
                                        ->schema([
                                            InfolistView::make('filament.infolists.client-private')
                                                ->columnSpanFull(),
                                        ])
                                        ->headerActions([
                                            // ✅ Add Action (when no data exists)
                                            InfolistAction::make('addPrivateInfo')
                                                ->label('Add')
                                                ->visible(fn ($record) =>
                                                    empty($record->private_info) && empty($record->review_date)
                                                )
                                                ->color('primary')
                                                ->icon('heroicon-o-plus')
                                                ->modalHeading('Add Private Info')
                                                ->modalSubmitActionLabel('Create')
                                                ->modalWidth('xl')
                                                ->form([
                                                    Textarea::make('private_info')
                                                        ->label('Private Info')
                                                        ->rows(5)
                                                        ->placeholder('Enter private client information...')
                                                        ->required(),

                                                    Forms\Components\DatePicker::make('review_date')
                                                        ->label('Replace Date')
                                                        ->displayFormat('d M Y')
                                                        ->required(),
                                                ])
                                                ->action(function (array $data, $record): void {
                                                    $record->update([
                                                        'private_info' => $data['private_info'],
                                                        'review_date'  => $data['review_date'],
                                                    ]);

                                                    Notification::make()
                                                        ->title('Private info added successfully.')
                                                        ->success()
                                                        ->send();
                                                }),

                                            // ✅ Edit Action (when data exists)
                                            InfolistAction::make('editPrivateInfo')
                                                ->label('Edit')
                                                ->visible(fn ($record) =>
                                                    !empty($record->private_info) || !empty($record->review_date)
                                                )
                                                ->color('primary')
                                                ->icon('heroicon-o-pencil')
                                                ->modalHeading('Edit Private Info')
                                                ->modalSubmitActionLabel('Update')
                                                ->modalWidth('xl')
                                                ->form([
                                                    Textarea::make('private_info')
                                                        ->label('Private Info')
                                                        ->rows(5)
                                                        ->default(fn ($record) => $record?->private_info)
                                                        ->placeholder('Enter private client information...'),

                                                    Forms\Components\DatePicker::make('review_date')
                                                        ->label('Replace Date')
                                                        ->displayFormat('d M Y')
                                                        ->default(fn ($record) => $record?->review_date),
                                                ])
                                                ->action(function (array $data, $record): void {
                                                    $record->update([
                                                        'private_info' => $data['private_info'],
                                                        'review_date'  => $data['review_date'],
                                                    ]);

                                                    Notification::make()
                                                        ->title('Private info updated successfully.')
                                                        ->success()
                                                        ->send();
                                                }),
                                        ]),
                            ])
                            ->columnSpan(3)

                    ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;']),
Section::make('')
->schema([
Section::make('Additional Information')
    ->schema(fn ($record) =>
        $record->additionalContacts()
            ->get()
            ->map(function ($contact, $index) {
                $headingPrefix = 'Additional Contact';
                if ($contact->primary_contact) {
                    $headingPrefix = 'Primary Contact';
                } elseif ($contact->billing_contact) {
                    $headingPrefix = 'Billing Contact';
                }

                return Section::make("{$headingPrefix} #" . ($index + 1))
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make("full_name_$index")
                                ->label('Full Name')
                                ->state(function () use ($contact) {
                                    $name = $contact->first_name;
                                    if ($contact->salutation) {
                                        $name = $contact->salutation . ' ' . $name;
                                    }
                                    $name .= ' ' . $contact->last_name;
                                    return $name;
                                })
                                ->weight('bold'),

                            TextEntry::make("relation_$index")
                                ->label('Relation')
                                ->state($contact->relation),
                        ]),

                        TextEntry::make("contact_$index")
                            ->label('Contact')
                            ->state(function () use ($contact) {
                                $contactInfo = [];
                                if ($contact->mobile_number) {
                                    $contactInfo[] = "📱 {$contact->mobile_number}";
                                }
                                if ($contact->phone_number) {
                                    $contactInfo[] = "-- 📞 {$contact->phone_number}";
                                }
                                return implode(' ', $contactInfo);
                            }),

                        TextEntry::make("email_$index")
                            ->label('Email')
                            ->state($contact->email),
                    ])
                   ->headerActions([
                    InfolistAction::make('view')
                        ->label('')
                         ->iconButton()
                        ->tooltip('View Contact')
                        ->icon('heroicon-s-eye')
                        ->color('warning')
                        ->modalHeading('Contact')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalContent(fn () => view('filament.infolists.contact-view', ['contact' => $contact])),
                    
                    InfolistAction::make('edit')
                        ->label('')
                         ->iconButton()
                        ->color('stripe')
                        ->tooltip('Edit Contact')
                        ->icon('heroicon-s-pencil-square')
                        ->mountUsing(fn ($form) => $form->fill($contact->toArray()))
                        ->form([
 Forms\Components\Grid::make(['default' => 5])
                    ->schema([
                        Forms\Components\Fieldset::make('Salutation')
                            ->schema([
                                Forms\Components\Checkbox::make('use_salutation')
                                    ->label('Use salutation')
                                    ->inline()
                                    ->default(true)
                                    ->reactive()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('salutation')
                                    ->options([
                                        'Mr' => 'Mr',
                                        'Mrs' => 'Mrs',
                                        'Miss' => 'Miss',
                                        'Ms' => 'Ms',
                                        'Mx' => 'Mx',
                                        'Doctor' => 'Doctor',
                                        'Them' => 'Them',
                                        'They' => 'They',
                                    ])
                                    ->placeholder('Select')
                                    ->disabled(fn($get) => !$get('use_salutation')),
                            ])
                            ->columnSpan(2),
                        Forms\Components\Fieldset::make('Client Info')
                            ->schema([
                                Forms\Components\TextInput::make('first_name')->label('First name')->placeholder('Enter First Name')->required(),
                                Forms\Components\TextInput::make('last_name')->label('Last name')->placeholder('Enter Last/Family Name')->required(),
                                Forms\Components\TextInput::make('email')->email()->label('Email Address')->placeholder('Enter Email')->required()->columnSpanFull(),
                            ])
                            ->columnSpan(3)
                            ->columns(2),
                    ]),
                Forms\Components\Fieldset::make('Address')
                    ->schema([
                        Textarea::make('address')->label('')->placeholder('Enter Address')->columnSpanFull(),
                    ]),
                Forms\Components\Fieldset::make('Additional Information')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 2])
                            ->schema([
                                Forms\Components\TextInput::make('relation')->placeholder('Enter Relation')->columnSpan(1),
                                Forms\Components\TextInput::make('unit_or_appartment_no')->placeholder('Enter Unit/Appartment Number')->columnSpan(1),
                            ]),
                    ]),
                Forms\Components\Fieldset::make('Contact')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 2])
                            ->schema([
                                Forms\Components\TextInput::make('mobile_number')->label('Mobile Number')->placeholder('Enter Mobile Number')->columnSpan(1),
                                Forms\Components\TextInput::make('phone_number')->placeholder('Phone Number')->columnSpan(1),
                            ]),
                    ]),
                Forms\Components\Fieldset::make('Company Info')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 2])
                            ->schema([
                                Forms\Components\TextInput::make('company_name')->label('Company Name')->placeholder('Enter Company Name')->columnSpan(1),
                                Forms\Components\TextInput::make('company_number')->placeholder('Company Number')->columnSpan(1),
                            ]),
                    ]),
                Forms\Components\Fieldset::make('Others Info')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 2])
                            ->schema([
                                Forms\Components\TextInput::make('purchase_order')->label('Purchase Order')->placeholder('Enter PO')->columnSpan(1),
                                Forms\Components\TextInput::make('reference_number')->placeholder('Enter Reference Number')->columnSpan(1),
                                Forms\Components\TextInput::make('custom_field')->placeholder('Enter Custom Field')->columnSpan(2),
                                Forms\Components\Checkbox::make('primary_contact')->columnSpan(1),
                                Forms\Components\Checkbox::make('billing_contact')->columnSpan(1),
                            ]),
                    ]),
                   
                        ])
                        ->action(function (array $data) use ($contact) {
                            $contact->update($data);
                        }),

                         InfolistAction::make('delete')
                            ->label('')
                         ->iconButton()
                            ->icon('heroicon-s-trash')
                            ->tooltip('Delete Contact')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Delete Contact')
                            ->modalDescription('Are you sure you want to delete this contact? This action cannot be undone.')
                            ->action(function () use ($contact) {
                                $contact->delete();

                                Notification::make()
                                    ->title('Contact deleted')
                                    ->success()
                                    ->send();

                                redirect(request()->header('Referer') ?? url()->previous());
                            }),
                       
                    ]);
            })
            ->toArray(),
            
    )
    ->columnSpan(2)
    ->headerActions([
        InfolistAction::make('add')
            ->label('ADD')
            ->size('sm')
            ->icon('heroicon-s-document-plus')
            ->color('primary')
            ->form([
                Forms\Components\Grid::make(['default' => 5])
                    ->schema([
                        Forms\Components\Fieldset::make('Salutation')
                            ->schema([
                                Forms\Components\Checkbox::make('use_salutation')
                                    ->label('Use salutation')
                                    ->inline()
                                    ->default(true)
                                    ->reactive()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('salutation')
                                    ->options([
                                        'Mr' => 'Mr',
                                        'Mrs' => 'Mrs',
                                        'Miss' => 'Miss',
                                        'Ms' => 'Ms',
                                        'Mx' => 'Mx',
                                        'Doctor' => 'Doctor',
                                        'Them' => 'Them',
                                        'They' => 'They',
                                    ])
                                    ->placeholder('Select')
                                    ->disabled(fn($get) => !$get('use_salutation')),
                            ])
                            ->columnSpan(2),
                        Forms\Components\Fieldset::make('Client Info')
                            ->schema([
                                Forms\Components\TextInput::make('first_name')->label('First name')->placeholder('Enter First Name')->required(),
                                Forms\Components\TextInput::make('last_name')->label('Last name')->placeholder('Enter Last/Family Name')->required(),
                                Forms\Components\TextInput::make('email')->email()->label('Email Address')->placeholder('Enter Email')->required()->columnSpanFull(),
                            ])
                            ->columnSpan(3)
                            ->columns(2),
                    ]),
                Forms\Components\Fieldset::make('Address')
                    ->schema([
                        Textarea::make('address')->label('')->placeholder('Enter Address')->columnSpanFull(),
                    ]),
                Forms\Components\Fieldset::make('Additional Information')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 2])
                            ->schema([
                                Forms\Components\TextInput::make('relation')->placeholder('Enter Relation')->columnSpan(1),
                                Forms\Components\TextInput::make('unit_or_appartment_no')->placeholder('Enter Unit/Appartment Number')->columnSpan(1),
                            ]),
                    ]),
                Forms\Components\Fieldset::make('Contact')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 2])
                            ->schema([
                                Forms\Components\TextInput::make('mobile_number')->label('Mobile Number')->placeholder('Enter Mobile Number')->columnSpan(1),
                                Forms\Components\TextInput::make('phone_number')->placeholder('Phone Number')->columnSpan(1),
                            ]),
                    ]),
                Forms\Components\Fieldset::make('Company Info')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 2])
                            ->schema([
                                Forms\Components\TextInput::make('company_name')->label('Company Name')->placeholder('Enter Company Name')->columnSpan(1),
                                Forms\Components\TextInput::make('company_number')->placeholder('Company Number')->columnSpan(1),
                            ]),
                    ]),
                Forms\Components\Fieldset::make('Others Info')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 2])
                            ->schema([
                                Forms\Components\TextInput::make('purchase_order')->label('Purchase Order')->placeholder('Enter PO')->columnSpan(1),
                                Forms\Components\TextInput::make('reference_number')->placeholder('Enter Reference Number')->columnSpan(1),
                                Forms\Components\TextInput::make('custom_field')->placeholder('Enter Custom Field')->columnSpan(2),
                                Forms\Components\Checkbox::make('primary_contact')->columnSpan(1),
                                Forms\Components\Checkbox::make('billing_contact')->columnSpan(1),
                            ]),
                    ]),
            ])
            ->icon('heroicon-m-plus')
            ->action(function (array $data, Client $record) {
                $user = Auth::user();
                AdditionalContact::create([
                    'user_id' => $user->id,
                    'client_id' => $record->id,
                    'salutation' => $data['salutation'] ?? null,
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'] ?? null,
                    'address' => $data['address'] ?? null,
                    'unit_or_appartment_no' => $data['unit_or_appartment_no'] ?? null,
                    'phone_number' => $data['phone_number'] ?? null,
                    'mobile_number' => $data['mobile_number'] ?? null,
                    'relation' => $data['relation'] ?? null,
                    'company_name' => $data['company_name'] ?? null,
                    'company_number' => $data['company_number'] ?? null,
                    'purchase_order' => $data['purchase_order'] ?? null,
                    'reference_number' => $data['reference_number'] ?? null,
                    'custom_field' => $data['custom_field'] ?? null,
                    'primary_contact' => $data['primary_contact'] ?? false,
                    'billing_contact' => $data['billing_contact'] ?? false,
                ]);

                Notification::make()
                    ->title('Additional contact created successfully')
                    ->success()
                    ->send();
            }),
            
        ]),


                            Section::make('Teams')
                        ->schema([
                            InfolistView::make('filament.infolists.client-teams')
                                                        ->columnSpanFull(),
                        ])
                        ->headerActions([
                                InfolistAction::make('Add_Team')
                                    ->label('Add')
                                    ->size('sm')
                                    ->color('primary')
                                    ->url( fn() => route('filament.admin.resources.teams.index'))   
                            ]),

                            Section::make('Settings')
                                ->schema([
                                    InfolistView::make('filament.infolists.client-setting')
                                                                ->columnSpanFull(),
                                ])
                                ->headerActions([
                                InfolistAction::make('Edit_Setting')
                                    ->label('Edit')
                                    ->size('sm')
                                    ->color('primary')
                                     ->url(fn ($record) => ClientResource::getUrl('edit', ['record' => $record]))
                                    ->openUrlInNewTab(false) 
                            ]),
])->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;'])
->columnSpan(2),
                        // Primary Contact Section


                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
            'view' => Pages\ViewClient::route('/{record}/view'),
        ];
    }
}
