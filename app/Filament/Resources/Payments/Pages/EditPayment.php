<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $success = app(\App\Services\PaymentService::class)->updateStatus($record->id, $data['status']);

        if (! $success) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Failed to update payout')
                ->body('The payout could not be updated. Ensure it is currently pending.')
                ->send();

            $this->halt();
        }

        $record->status = $data['status'];

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
