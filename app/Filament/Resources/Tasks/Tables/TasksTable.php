<?php

namespace App\Filament\Resources\Tasks\Tables;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->withCount('images')
                ->with('previewImage')
            )
            ->columns([
                ImageColumn::make('preview')
                    ->label('Image')
                    ->getStateUsing(fn(Task $r) => optional($r->images->first())->url)
                    ->circular(),
                TextColumn::make('title')->searchable()->limit(40)->wrap(),

                TextColumn::make('user.name')
                ->label('Assigned User')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(TaskStatus $state) => match ($state) {
                        TaskStatus::Pending => 'warning',
                        TaskStatus::In_progress => 'info',
                        TaskStatus::Completed => 'success',
                    })
                    ->formatStateUsing(fn(TaskStatus $state) => Str::of($state->value)->replace('_', ' ')->title()),
                TextColumn::make('images_count')->label('Images'),
                TextColumn::make('created_at')->dateTime()->since(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    TaskStatus::Pending->value => 'Pending',
                    TaskStatus::In_progress->value => 'In Progress',
                    TaskStatus::Completed->value => 'Completed',
                ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
