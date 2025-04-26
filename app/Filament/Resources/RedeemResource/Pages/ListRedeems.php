<?php

namespace App\Filament\Resources\RedeemResource\Pages;

use App\Filament\Resources\RedeemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRedeems extends ListRecords
{
    protected static string $resource = RedeemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
