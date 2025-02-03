<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletResource\Pages;
use App\Filament\Resources\WalletResource\RelationManagers;
use App\Models\Wallet;
use App\Services\WalletService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;
use Illuminate\Support\Number;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('balance')->required()->numeric()->hiddenOn('edit'),
                Select::make('currency')->options([
                    'EGP' => 'EGP',
                    'USD' => 'USD',
                ])->hiddenOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('name'),
                TextColumn::make('balance')->formatStateUsing(fn($state, $record) => Number::currency($state, $record->currency)),
                TextColumn::make('currency'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('correct-balance')
                    ->label('Correct')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->form([
                        TextInput::make('amount')->required(),
                        Select::make('type')->options([
                            'actual_balance' => 'Actual Balance',
                            'error_amount' => 'Error Amount',
                        ])->required()->default('actual_balance'),
                    ])
                    ->action(function (array $data, Wallet $record, WalletService $service) {
                        $request = Request::create('', '', [
                            $data['type'] => $data['amount'],
                        ]);
                        $service->correctBalance($record, $request);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RecordsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWallets::route('/'),
            'show' => Pages\ViewWallet::route('/{record}'),
        ];
    }
}
