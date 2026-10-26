<?php

namespace App\Http\Livewire\UserLevel;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\UserLevel;

class Datatable extends DataTableComponent
{
    protected $model = UserLevel::class;

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
            Column::make("Level", "level")
                ->sortable()
                ->searchable(),
            Column::make('Tunjangan', 'tunjangan')
                ->view('components.table.custom.custom_currency')
                ->sortable()
                ->searchable(),
            Column::make("Persentase Kinerja", "persentase_kinerja")
                ->view('components.table.custom.percentage')
                ->sortable()
                ->searchable(),
            Column::make("Persentase Kedisiplinan", "persentase_kedisiplinan")
                ->view('components.table.custom.percentage')
                ->sortable()
                ->searchable(),
            Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
                ),
        ];
    }
}
