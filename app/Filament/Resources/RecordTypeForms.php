<?php

namespace App\Filament\Resources;

use App\Models\Record;
use App\Models\Strategy;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

trait RecordTypeForms
{

    /**
     * @return array
     */
    private function payForm(): array
    {
        return [
            TextInput::make('name'),
            TextInput::make('amount')
                ->numeric()
                ->required(),
            DateTimePicker::make('date')
                ->maxDate(now())
                ->default(now())
                ->required(),
            Select::make('related_to')
                ->label('Related Wallet')
                ->required()
                ->relationship(
                    name: 'relatedWallet',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn($query) => Strategy::isActive()->first()->wallets()
                ),
            Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name')
                ->createOptionForm([
                    TextInput::make('name')->required(),
                    Select::make('parent_id')
                        ->label('Parent Category')
                        ->relationship('parent', 'name')
                ])
        ];
    }

    /**
     * @return array
     */
    private function topupForm(): array
    {
        return [
            TextInput::make('name'),
            TextInput::make('amount')
                ->numeric()
                ->required(),
            DateTimePicker::make('date')
                ->maxDate(now())
                ->default(now())
                ->required(),
            Select::make('related_to')
                ->label('Related Wallet')
                ->relationship(
                    name: 'relatedWallet',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn($query) => Strategy::isActive()->first()->wallets()
                ),
            Select::make('category_id')
                ->label('Category')
                ->relationship('category', 'name')
                ->createOptionForm([
                    TextInput::make('name')->required(),
                    Select::make('parent_id')
                        ->label('Parent Category')
                        ->relationship('parent', 'name')
                ])
        ];
    }

    private function transferForm(): array
    {
        return [
            TextInput::make('name'),
            TextInput::make('amount')->numeric()->required(),
            DateTimePicker::make('date')->maxDate(now())->required()->default(now()),
            Select::make('sender_wallet')
                ->relationship('transfer.senderWallet', 'name')
                ->label('Sender Wallet')
                ->required()
                ->default(fn(?Record $record) => $record?->transfer ? $record->transfer->sender_wallet : null),
            Select::make('receiver_wallet')
                ->relationship('transfer.receiverWallet', 'name')
                ->label('Receiver Wallet')
                ->required()
                ->default(fn(?Record $record) => $record?->transfer ? $record->transfer->receiver_wallet : null),
        ];
    }
}
