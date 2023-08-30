<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewRecordRequest;
use App\Http\Requests\TransferRecordRequest;
use App\Http\Resources\RecordResource;
use App\Models\Balance;
use App\Models\BalancePerDate;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Record;
use App\Models\Transfer;
use App\Models\Wallet;
use App\Services\NewRecord;
use App\Services\TransferRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecordController extends Controller
{

    public function index(Wallet $wallet)
    {
        $records = $wallet->records;
        return RecordResource::collection($records);
    }

    public function pay(Wallet $wallet, NewRecord $service, NewRecordRequest $request)
    {
        return $service->pay($wallet, $request);
    }

    public function topup(Wallet $wallet, NewRecord $service, NewRecordRequest $request)
    {
        return $service->topup($wallet, $request);
    }

    public function transfer(Wallet $wallet, TransferRecord $service,TransferRecordRequest $request)
    {
        return $service->transfer($wallet,$request);
    }



    public function update(Wallet $wallet, Record $record)
    {
        return DB::transaction(function () use ($wallet, $record) {
            switch ($record->type) {
                case ('Expense'):
                    if (\request('type') == 'Expense') {
                        $record->balance->update([
                            'value' => $record->balance->value + $record->amount - \request('amount')
                        ]);
                        $record->update([
                            'amount' => \request('amount'),
                            'description' => \request('description'),
                            'type' => 'Expense',
                            'balance_id' => \request('balance'),
                            'category_id' => \request('category'),
                            'wallet_id' => $wallet->id,
                            'currency_id' => $record->balance->currency->id,
                            'balance_after' => $record->balance->value,
                            'date' => \request('date') ?? $record->date
                        ]);
                    } else {
                        $record->balance->update([
                            'value' => $record->balance->value + $record->amount + \request('amount')
                        ]);
                        $record->update([
                            'amount' => \request('amount'),
                            'description' => \request('description'),
                            'type' => 'Income',
                            'balance_id' => \request('balance'),
                            'category_id' => \request('category'),
                            'wallet_id' => $wallet->id,
                            'currency_id' => $record->balance->currency->id,
                            'balance_after' => $record->balance->value,
                            'date' => \request('date') ?? $record->date
                        ]);
                    }
                    BalancePerDate::updateOrCreate(
                        [
                            'date' => $record->date,
                            'wallet_id' => $wallet->id,
                            'balance_id' => $record->balance->id
                        ],
                        ['value' => $record->balance->value]
                    );
                    return redirect()->route('records', ['wallet' => $wallet->id])->with('message', 'Record updated successfully');
                    break;
                case ('Income'):
                    if (\request('type') == 'Expense') {
                        $record->balance->update([
                            'value' => $record->balance->value - $record->amount - \request('amount')
                        ]);
                        $record->update([
                            'amount' => \request('amount'),
                            'description' => \request('description'),
                            'type' => 'Expense',
                            'balance_id' => \request('balance'),
                            'category_id' => \request('category'),
                            'wallet_id' => $wallet->id,
                            'currency_id' => $record->balance->currency->id,
                            'balance_after' => $record->balance->value,
                            'date' => \request('date') ?? $record->date
                        ]);
                    } else {
                        $record->balance->update([
                            'value' => $record->balance->value + \request('amount') - $record->amount
                        ]);
                        $record->update([
                            'amount' => \request('amount'),
                            'description' => \request('description'),
                            'type' => 'Income',
                            'balance_id' => \request('balance'),
                            'category_id' => \request('category'),
                            'wallet_id' => $wallet->id,
                            'currency_id' => $record->balance->currency->id,
                            'balance_after' => $record->balance->value,
                            'date' => \request('date') ?? $record->date
                        ]);
                    }
                    BalancePerDate::updateOrCreate(
                        [
                            'date' => $record->date,
                            'wallet_id' => $wallet->id,
                            'balance_id' => $record->balance->id
                        ],
                        ['value' => $record->balance->value]
                    );
                    return redirect()->route('records', ['wallet' => $wallet->id])->with('message', 'Record updated successfully');
                    break;
                default:
                    $amountChanged = request('amount') != $record->amount;
                    $walletsChanged = request('sender') != $record->transfer->sender_wallet || request('receiver') != $record->transfer->receiver_wallet;
                    if (!$amountChanged && !$walletsChanged)
                        return redirect()->back();

                    $oldSenderBalance = Balance::find($record->transfer->sender_balance);
                    $oldReceiverBalance = Balance::find($record->transfer->receiver_balance);

                    $senderWallet = Wallet::find(\request('sender'));
                    $receiverWallet = Wallet::find(\request('receiver'));
                    /*800*/
                    $oldSenderBalance->update(['value' => $oldSenderBalance->value + $record->amount]); //800+200=1000// Return to original state
                    /*700*/
                    $oldReceiverBalance->update(['value' => $oldReceiverBalance->value - $record->amount]); //700-200=500 // Return to original state


                    $newSenderBalance = $senderWallet->balances()->where('currency_id', \request('currency'))->limit(1)->first();
                    $newReceiverBalance = $receiverWallet->balances()->where('currency_id', \request('currency'))->limit(1)->first();

                    /*500*/
                    $newSenderBalance->update(['value' => $newSenderBalance->value - request('amount')]); //500-200=300
                    /*1000*/
                    $newReceiverBalance->update(['value' => $newReceiverBalance->value + request('amount')]); //1000+200=1200

                    BalancePerDate::updateOrCreate(
                        [
                            'date' => $record->date,
                            'wallet_id' => $senderWallet->id,
                            'balance_id' => $newSenderBalance->id
                        ],
                        ['value' => $newSenderBalance->value]
                    );

                    BalancePerDate::updateOrCreate(
                        [
                            'date' => $record->date,
                            'wallet_id' => $receiverWallet->id,
                            'balance_id' => $newReceiverBalance->id
                        ],
                        ['value' => $newReceiverBalance->value]
                    );
                    $record->update([
                        'amount' => \request('amount'),
                        'type' => 'Transfer',
                        'balance_id' => $newSenderBalance->id,
                        'wallet_id' => $senderWallet->id,
                        'currency_id' => $record->balance->currency->id,
                        'date' => \request('date') ?? $record->date
                    ]);
                    $record->transfer->update([
                        'amount' => \request('amount'),
                        'sender_wallet' => $senderWallet->id,
                        'receiver_wallet' => $receiverWallet->id,
                        'sender_balance' => $newSenderBalance->id,
                        'receiver_balance' => $newReceiverBalance->id,
                    ]);
                    return redirect()->route('records', ['wallet' => $wallet->id])->with('message', 'Record updated successfully');
            }
        });
    }

    public function delete(Wallet $wallet, Record $record)
    {
        return DB::transaction(function () use ($record, $wallet) {
            switch ($record->type) {
                case ('Expense'):
                    $record->balance->update(['value' => $record->balance->value + $record->amount]);
                    BalancePerDate::updateOrCreate(
                        [
                            'date' => $record->date,
                            'wallet_id' => $wallet->id,
                            'balance_id' => $record->balance->id
                        ],
                        ['value' => $record->balance->value]
                    );
                    $record->delete();
                    break;
                case ('Income'):
                    $record->balance->update(['value' => $record->balance->value - $record->amount]);
                    BalancePerDate::updateOrCreate(
                        [
                            'date' => $record->date,
                            'wallet_id' => $wallet->id,
                            'balance_id' => $record->balance->id
                        ],
                        ['value' => $record->balance->value]
                    );
                    $record->delete();
                    break;
                default:
                    $senderBalance = Balance::find($record->transfer->sender_balance);
                    $receiverBalance = Balance::find($record->transfer->receiver_balance);

                    $senderBalance->update(['value' => $senderBalance->value + $record->amount]);
                    $receiverBalance->update(['value' => $receiverBalance->value - $record->amount]);

                    BalancePerDate::updateOrCreate(
                        [
                            'date' => $record->date,
                            'wallet_id' => $record->transfer->sender_wallet,
                            'balance_id' => $senderBalance->id
                        ],
                        ['value' => $senderBalance->value]
                    );
                    BalancePerDate::updateOrCreate(
                        [
                            'date' => $record->date,
                            'wallet_id' => $record->transfer->receiver_wallet,
                            'balance_id' => $receiverBalance->id
                        ],
                        ['value' => $receiverBalance->value]
                    );

                    $record->delete();
            }
            return redirect()->route('records', ['wallet' => $wallet->id])->with('message', 'Record deleted successfully');
        });
    }
}
