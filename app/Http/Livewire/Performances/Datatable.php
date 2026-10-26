<?php

namespace App\Http\Livewire\Performances;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Performance;

class Datatable extends DataTableComponent
{

    public function builder(): Builder
    {
        return Performance::query()->where('user_id', session('sendUserId'))->select('performances.*');
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
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Indikator", "indikator")
                ->sortable(),
            Column::make("Bobot", "bobot")
                ->sortable()
                ->footer(fn ($rows) => 'Total Bobot : ' . $rows->sum('bobot')),
            Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
                ),
        ];
    }
}
