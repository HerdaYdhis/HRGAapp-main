<?php

namespace App\Http\Livewire\Department;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Department;

class Datatable extends DataTableComponent
{
    protected $model = Department::class;

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
            Column::make("Nama Department", "name")
                ->sortable(),
            Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
                ),
        ];
    }
}
