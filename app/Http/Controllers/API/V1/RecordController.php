<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\RecordType;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayRequest;
use App\Http\Requests\UpdateRecordRequest;
use App\Http\Requests\TransferRecordRequest;
use App\Http\Resources\RecordResource;
use App\Models\Balance;
use App\Models\BalancePerDate;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Record;
use App\Models\Transfer;
use App\Models\Wallet;
use App\Services\EditRecord;
use App\Services\EditTransfer;
use App\Services\NewRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecordController extends Controller
{
    public function __construct(private NewRecord $createService, private EditRecord $updateService)
    {
    }

    public function index(Wallet $wallet)
    {
        $records = $wallet->records;
        return RecordResource::collection($records);
    }

    public function pay(Wallet $wallet, PayRequest $request): JsonResponse
    {
        return $this->createService->pay($wallet, $request);
    }

    public function topup(Request $request, ?int $walletId = null): JsonResponse
    {
        return $this->createService->topup($walletId, $request);
    }

    /**
     * @throws \Throwable
     */
    public function transfer(TransferRecordRequest $request): JsonResponse
    {
        return $this->createService->transfer($request);
    }


    public function updateRecord(Record $record, UpdateRecordRequest $request)
    {
        return $this->updateService->editRecord($record, $request);
    }

    /**
     * @throws \Throwable
     */
    public function updateTransfer(Record $record, EditTransfer $service, TransferRecordRequest $request)
    {
        return $service->editTransferRecord($record, $request);
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
