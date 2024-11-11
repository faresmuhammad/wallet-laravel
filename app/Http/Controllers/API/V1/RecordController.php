<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\RecordType;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayRequest;
use App\Http\Requests\UpdateRecordRequest;
use App\Http\Requests\TransferRecordRequest;
use App\Http\Resources\RecordResource;
use App\Models\Record;
use App\Models\Wallet;
use App\Services\DeleteRecordService;
use App\Services\EditRecord;
use App\Services\EditTransfer;
use App\Services\NewRecord;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        return $this->createService->pay($wallet, $request->validated());
    }

    public function topup(Request $request, ?int $walletId = null): JsonResponse
    {
        $data = $request->validate([
            'amount' => 'numeric|required',
            'name' => 'string',
            'category_id' => 'integer|exists:categories,id|nullable',
            'date' => 'date|nullable',
        ]);
        return $this->createService->topup($walletId, $data);
    }

    /**
     * @throws \Throwable
     */
    public function transfer(TransferRecordRequest $request): JsonResponse
    {
        return $this->createService->transfer($request->validated());
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
