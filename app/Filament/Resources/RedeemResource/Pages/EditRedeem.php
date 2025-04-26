<?php

namespace App\Filament\Resources\RedeemResource\Pages;

use App\Filament\Resources\RedeemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRedeem extends EditRecord
{
    protected static string $resource = RedeemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
