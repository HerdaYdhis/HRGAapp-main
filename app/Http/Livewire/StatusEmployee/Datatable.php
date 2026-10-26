<?php

namespace App\Http\Livewire\StatusEmployee;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\EmployeeStatus;

class Datatable extends DataTableComponent
{
    protected $model = EmployeeStatus::class;

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
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Employee Status", "name")
                ->sortable(),
            Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
                ),
        ];
    }
}
