<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Enums\RecordType;
use App\Filament\Resources\RecordResource;
use App\Filament\Resources\RecordTypeForms;
use App\Models\Record;
use App\Services\DeleteRecordService;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord as BaseEditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditRecord extends BaseEditRecord
{
    use RecordTypeForms;

    protected static string $resource = RecordResource::class;

    public function form(Form $form): Form
    {
        return $form->schema(match ($form->getRecord()->type) {
            RecordType::Income => $this->topupForm(),
            RecordType::Expense => $this->payForm(),
            RecordType::Transfer => $this->transferForm(),
        });
    }

    /**
     * @throws \Throwable
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $service = match ($record->type){
            RecordType::Expense,RecordType::Income => app(\App\Services\EditRecord::class),
            RecordType::Transfer => app(\App\Services\EditTransfer::class),
            default => throw new \Exception('Error in record type')
        };
        $service->editRecord($record, $data);
        return $record;
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function (Actions\DeleteAction $action, Record $record, DeleteRecordService $service) {
                    $result = $action->process(static function () use ($record, $service) {
                        return match ($record->type) {
                            RecordType::Expense => $service->deleteExpenseRecord($record),
                            RecordType::Income => $service->deleteIncomeRecord($record),
                            RecordType::Transfer => $service->deleteTransferRecord($record),
                            default => throw new HttpResponseException(apiResponse("Unknown Error with the record type", status: 400)),
                        };
                    });

                    if (!$result) {
                        $action->failure();

                        return;
                    }

                    $action->success();
                })
                ->after(fn() => back()),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->form->fill([
            'name' => $this->record->name,
            'amount' => $this->record->amount,
            'date' => $this->record->date,
            'sender_wallet' => $this->record->transfer->sender_wallet,
            'receiver_wallet' => $this->record->transfer->receiver_wallet,
        ]);
    }

}
