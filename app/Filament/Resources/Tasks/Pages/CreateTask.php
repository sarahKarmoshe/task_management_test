<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
}
