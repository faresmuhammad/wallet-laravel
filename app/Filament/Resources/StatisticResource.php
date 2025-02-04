<?php

namespace App\Filament\Resources;

use App\Enums\Period;
use App\Filament\Resources\StatisticResource\Pages;
use App\Filament\Resources\StatisticResource\RelationManagers;
use App\Models\Statistic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StatisticResource extends Resource
{
    protected static ?string $model = Statistic::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\Select::make('type')->required()->options([
                    'sum' => 'Sum',
                    'average' => 'Average',
                ])->default('sum'),
                Forms\Components\DatePicker::make('start_date')->format('Y-m-d'),
                Forms\Components\DatePicker::make('end_date')->after('start_date')->format('Y-m-d'),
                Forms\Components\Select::make('show_by')->options(Period::class)->required()->default(Period::Month),
                Forms\Components\Select::make('filter_by')->options([
                    'category' => 'Category',
                    'label' => 'Label',
                    'both' => 'Both',
                ])->default('both'),
                Forms\Components\Select::make('categories')
                    ->relationship(
                        'categories',
                        'name',
                        fn(Builder $query): Builder => $query->whereNull('user_id')->orWhere('user_id', auth()->id())
                    )
                    ->multiple(),
                Forms\Components\Select::make('labels')
                    ->relationship(
                        'labels',
                        'name',
                        fn(Builder $query): Builder => $query->whereNull('user_id')->orWhere('user_id', auth()->id())
                    )
                    ->multiple()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('type')->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('start_date')->date('Y-m-d'),
                Tables\Columns\TextColumn::make('end_date')->date('Y-m-d'),
                Tables\Columns\TextColumn::make('show_by')->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('filter_by')->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('categories.name')->badge()->color('info'),
                Tables\Columns\TextColumn::make('labels.name')->badge()->color('gray'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false)
            ->query(fn() => Statistic::where('user_id', auth()->id()));
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
            'index' => Pages\ListStatistics::route('/'),
        ];
    }
}
