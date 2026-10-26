<?php

namespace App\Http\Livewire\MasterTask;

use Carbon\Carbon;
use App\Models\MasterTask;
use App\Utilities\UserAccess;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class Datatable extends DataTableComponent
{
    public function builder(): Builder
    {
        return MasterTask::query()->with('user')->where('user_id', auth()->user()->id)->select('master_tasks.*');
    }


    protected $listeners = [
        're_render_table' => '$refresh'
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
        $this->setEagerLoadAllRelationsEnabled();
    }

    public function columns(): array
    {
        $column =  [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Title", "title")
                ->sortable()
                ->searchable(),
            Column::make("Description", "description")
            ->sortable()
                ->searchable(),
            Column::make("Type", "type")
            ->sortable()
                ->searchable(),
            Column::make("User", "user.name")
                ->sortable()
                ->searchable(),
            Column::make("Due Date", "duedate")
            ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return $value ? Carbon::parse($value)->locale('id')->isoFormat('LL') : '';
                }),
            Column::make("Created at", "created_at")
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return Carbon::parse($value)->locale('id')->isoFormat('LL LT'); // 'LL' untuk tanggal, 'LT' untuk waktu
                }),
            Column::make("Updated at", "updated_at")
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return Carbon::parse($value)->locale('id')->isoFormat('LL LT'); // 'LL' untuk tanggal, 'LT' untuk waktu
                }),
        ];
        if (UserAccess::can('mastertask', 'manage-mastertask') || auth()->user()->is_superadmin) {
            $column[] = Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
                );
        }
        return $column;
    }
}
