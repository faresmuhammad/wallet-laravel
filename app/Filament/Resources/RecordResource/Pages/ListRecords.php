<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Enums\RecordType;
use App\Filament\Resources\RecordResource;
use App\Filament\Resources\RecordTypeForms;
use App\Http\Requests\PayRequest;
use App\Http\Requests\TransferRecordRequest;
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
use Illuminate\Http\Request;

class ListRecords extends BaseListRecords
{

    use RecordTypeForms;

    protected static string $resource = RecordResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pay')
                ->label('Pay')
                ->color('danger')
                ->form($this->payForm())
                ->action(function (array $data, NewRecord $service) {
                    $request = PayRequest::create('','',$data);
                    $service->pay(Wallet::find($request->wallet_id), $request);
                }),
            Actions\Action::make('topup')
                ->label('Top Up')
                ->color('success')
                ->form($this->topupForm())
                ->action(function (array $data, NewRecord $service) {
                    $request = Request::create('','',$data);
                    $service->topup(Wallet::find($request->wallet_id), $request);
                }),
            Actions\Action::make('transfer')
                ->label('Transfer')
                ->color('warning')
                ->form($this->transferForm())
                ->action(function (array $data, NewRecord $service) {
                    $request = TransferRecordRequest::create('','',$data);
                    $service->transfer($request);
                })


        ];
    }

}
