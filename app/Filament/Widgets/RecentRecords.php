<?php

namespace App\Filament\Widgets;

use App\Enums\RecordType;
use App\Models\Record;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Number;

class RecentRecords extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('amount')->formatStateUsing(fn($state, $record) => Number::currency($state, $record->currency)),
                TextColumn::make('date')->dateTime(),
                TextColumn::make('type')
                    ->color(fn($state): string => match ($state) {
                        RecordType::Income => 'success',
                        RecordType::Expense => 'danger',
                        RecordType::Transfer => 'warning',
                    })
                    ->badge(),
            ])
            ->query(Record::where('date', '>=', now()->subDays(7))->limit(5))
            ->paginated(false);
    }
}
