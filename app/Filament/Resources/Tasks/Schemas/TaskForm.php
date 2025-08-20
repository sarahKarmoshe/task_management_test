<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('user_id')
                ->label('Assigned User')
                ->relationship(
                    name: 'user',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn ($query) => $query->where('role', 'user')
                )
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('title')->required()->maxLength(255),
            Textarea::make('description')->required()->rows(4),
            Select::make('status')
                ->required()
                ->options([
                    TaskStatus::Pending->value => 'Pending',
                    TaskStatus::In_progress->value => 'In Progress',
                    TaskStatus::Completed->value => 'Completed',
                ])
                ->native(false),


           Repeater::make('images')
                ->relationship()
                ->label('Images')
                ->collapsed()
                ->addActionLabel('Add image')
                ->schema([
                    FileUpload::make('path')
                        ->image()
                        ->required()
                        ->disk('public')
                        ->visibility('public')
                        ->directory("tasks/images")

                        ->getUploadedFileNameForStorageUsing(
                            fn (TemporaryUploadedFile $file): string =>
                                Str::uuid().'.'.($file->guessExtension() ?? $file->getClientOriginalExtension())
                        )

                        ->afterStateUpdated(function ($state, Set $set) {
                            if ($state instanceof TemporaryUploadedFile) {
                                $set('original_name', $state->getClientOriginalName());
                            }
                        }),

                    TextInput::make('original_name')
                        ->label('Original name')
                        ->disabled()
                        ->dehydrated(),
                ])

        ]);
    }
}
