<?php

namespace App\Filament\Resources;

use App\Enums\RecordType;
use App\Filament\Resources\RecordResource\Pages;
use App\Filament\Resources\RecordResource\RelationManagers;
use App\Models\Record;
use App\Models\Wallet;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Number;

class RecordResource extends Resource
{
    protected static ?string $model = Record::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('amount')->formatStateUsing(fn($state, $record) => Number::currency($state, $record->currency)),
                Tables\Columns\TextColumn::make('type')
                    ->color(fn($state): string => match ($state) {
                        RecordType::Income => 'success',
                        RecordType::Expense => 'danger',
                        RecordType::Transfer => 'warning',
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('date')
                    ->dateTimeTooltip('Y-m-d h:i A')
                    ->since(),
                Tables\Columns\TextColumn::make('wallet.name')
                    ->label('Wallet')
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        'EGP' => 'warning',
                        'USD' => 'success',
                        default => 'danger',
                    })
                    ->action(fn(Record $record) => redirect(route('filament.wallet.resources.wallets.show', ['record' => $record->wallet_id])))

            ])
            ->filters([

            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated()
            ->defaultSort('date', 'desc');
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
            'index' => Pages\ListRecords::route('/'),
            'edit' => Pages\EditRecord::route('/{record}/edit'),
        ];
    }
}
