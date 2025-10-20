<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\User;
use App\Models\Company;
use App\Models\StaffProfile;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentCategory;
use App\Models\Document;
use Carbon\Carbon;

class Compliance extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-s-document-check';

    protected static string $view = 'filament.pages.compliance';

    protected static ?string $navigationGroup = 'Reports';

     public function table(Table $table): Table
    {
        $authUser = Auth::user();

        $companyId = Company::where('user_id', $authUser->id)->value('id');

        if (!$companyId) {
            $query = User::query()->whereRaw('1=0'); 
        } else {
            $staffUserIds = StaffProfile::where('company_id', $companyId)
                ->where('is_archive', 'Unarchive')
                ->pluck('user_id');

            $query = User::whereIn('id', $staffUserIds)
                ->role('staff');

            if (! $staffUserIds->contains($authUser->id)) {
                $query = User::whereIn('id', $staffUserIds->push($authUser->id))
                    ->role('staff');
            }
        }

          $categories = DocumentCategory::query()
         ->where('company_id', $companyId)
        ->where('is_staff_doc', 1)
        ->where('is_compliance', 1)
        ->get();

    return $table
        ->query($query)
        ->columns(array_merge([
            TextColumn::make('name')
                ->label('Staff Name')
                ->searchable(),
        ], 
        $categories->map(function ($category) {
            return 
            TextColumn::make('category_'.$category->id)
                    ->label($category->name)
                    ->getStateUsing(function ($record) use ($category) {
                        $document = \App\Models\Document::where('user_id', $record->id)
                            ->where('document_category_id', $category->id)
                            ->first();

                        if (! $document) {
                            return '-';
                        }

                        if ($document->no_expiration) {
                            return 'No Expiration';
                        }

                        return $document->expired_at
                            ? Carbon::parse($document->expired_at)->format('d/m/Y')
                            : '-';
                    })
                    ->badge()
                    ->colors([
                        'rado' => fn ($state): bool => $state === 'No Expiration' || $state !== '-',
                        'blackk' => fn ($state): bool => $state === '-' || $state === null,
                    ])
                ->sortable();
        })->toArray()));
    }
}
