<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Filament\Resources\WalletResource;
use App\Models\Wallet;
use App\Services\WalletService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Http\Request;

class ViewWallet extends ViewRecord
{
    protected static string $resource = WalletResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('correct-balance')
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
                    redirect(route('filament.wallet.resources.wallets.show', ['record' => $record->id]));
                }),

        ];
    }


}
