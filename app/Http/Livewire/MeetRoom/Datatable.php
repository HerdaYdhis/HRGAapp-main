<?php

namespace App\Http\Livewire\Meetroom;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Meetroom;

class Datatable extends DataTableComponent
{
    protected $model = Meetroom::class;

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
            Column::make("Nama Ruangan", "name")
                ->sortable()
                ->searchable(),
            Column::make("Lokasi", "location")
                ->sortable()
                ->searchable(),
            Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
                ),
        ];
    }
}
