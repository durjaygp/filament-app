<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Owner;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Laravel\Prompts\SearchPrompt;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\DatePicker::make('date_of_birth'),

                Forms\Components\Select::make('owner_id')
                    ->label('Owner')
                    ->options(Owner::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        'cat' => 'Cat',
                        'dog' => 'Dog',
                        'rabbit' => 'Rabbit',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('date_of_birth')->searchable(),
                TextColumn::make('owner.name')->searchable(),
                TextColumn::make('type')->searchable()

            ])
            ->filters([
                // Filter by Type (e.g. Dog, Cat, etc.)
                SelectFilter::make('type')
                    ->options([
                        'Dog' => 'Dog',
                        'Cat' => 'Cat',
                        'Bird' => 'Bird',
                        'Reptile' => 'Reptile',
                    ])
                    ->label('Patient Type'),

                // Filter by Owner
                SelectFilter::make('owner_id')
                    ->relationship('owner', 'name')
                    ->label('Owner'),

                // Filter by Date of Birth - Show only patients born after a certain year
                Filter::make('born_after_2015')
                    ->label('Born After 2015')
                    ->query(fn (Builder $query): Builder =>
                    $query->where('date_of_birth', '>', Carbon::createFromDate(2015, 1, 1))
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
