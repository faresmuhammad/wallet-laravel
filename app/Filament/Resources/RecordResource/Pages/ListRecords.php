<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Enums\RecordType;
use App\Filament\Resources\RecordResource;
use App\Models\Category;
use App\Models\Record;
use App\Models\Strategy;
use App\Models\Wallet;
use App\Services\NewRecord;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords as BaseListRecords;
use GuzzleHttp\Client;

class ListRecords extends BaseListRecords
{
    protected static string $resource = RecordResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pay')
                ->label('Pay')
                ->color('danger')
                ->form($this->payForm())
                ->mutateFormDataUsing(function (array $data) {
                    $data['type'] = RecordType::Expense;
                    return $data;
                })
                ->action(function (array $data, NewRecord $service) {
                    $service->pay(Wallet::find($data['related_to']), $data);
                }),
            Actions\Action::make('topup')
                ->label('Top Up')
                ->color('success')
                ->form($this->topupForm())
                ->mutateFormDataUsing(function (array $data) {
                    $data['type'] = RecordType::Income;
                    return $data;
                })
                ->action(function (array $data, NewRecord $service) {
                    $service->topup($data['related_to'], $data);
                }),
            Actions\Action::make('transfer')
                ->label('Transfer')
                ->color('warning')
                ->form($this->transferForm())
                ->mutateFormDataUsing(function (array $data) {
                    $data['type'] = RecordType::Transfer;
                    return $data;
                })
                ->action(function (array $data, NewRecord $service) {
                    $service->transfer($data);
                })


        ];
    }

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
                ->required(),
            Select::make('receiver_wallet')
                ->relationship('transfer.receiverWallet', 'name')
                ->label('Receiver Wallet')
                ->required(),
        ];
    }

}
