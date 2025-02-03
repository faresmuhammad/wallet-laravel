<?php

namespace App\Filament\Resources\WalletResource\RelationManagers;

use App\Enums\RecordType;
use App\Filament\Resources\RecordTypeForms;
use App\Http\Requests\PayRequest;
use App\Http\Requests\TransferRecordRequest;
use App\Models\Wallet;
use App\Services\NewRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;
use Illuminate\Support\Number;

class RecordsRelationManager extends RelationManager
{
    use RecordTypeForms;

    protected static string $relationship = 'records';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
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
                    ->since()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('pay')
                    ->label('Pay')
                    ->color('danger')
                    ->form($this->payForm(true))
                    ->action(function (array $data, NewRecord $service) {
                        $request = PayRequest::create('', '', $data);
                        $service->pay(Wallet::find($this->ownerRecord->id), $request);
                    }),
                Action::make('topup')
                    ->label('Top Up')
                    ->color('success')
                    ->form($this->topupForm(true))
                    ->action(function (array $data, NewRecord $service) {
                        $request = Request::create('', '', $data);
                        $service->topup(Wallet::find($this->ownerRecord->id), $request);
                    }),
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
            ->defaultSort('date', 'desc');
    }
}
