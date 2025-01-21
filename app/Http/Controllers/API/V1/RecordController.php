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
use App\Services\DeleteRecordService;
use App\Services\EditRecord;
use App\Services\EditTransfer;
use App\Services\NewRecord;
use Illuminate\Http\Exceptions\HttpResponseException;
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

    public function topup(Request $request, Wallet $wallet): JsonResponse
    {
        return $this->createService->topup($wallet, $request);
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


    public function delete(Record $record, DeleteRecordService $service): JsonResponse
    {
        return match ($record->type) {
            RecordType::Expense => $service->deleteExpenseRecord($record),
            RecordType::Income => $service->deleteIncomeRecord($record),
            RecordType::Transfer => $service->deleteTransferRecord($record),
            default => throw new HttpResponseException(apiResponse("Unknown Error with the record type", status: 400)),
        };
    }
}
