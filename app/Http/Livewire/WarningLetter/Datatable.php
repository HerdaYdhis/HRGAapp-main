<?php

namespace App\Http\Livewire\WarningLetter;

use App\Models\WarningLetter;
use App\Utilities\UserAccess;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class Datatable extends DataTableComponent
{
    // protected $model = Evaluation::class;

    public function builder(): Builder
    {
        return WarningLetter::query()->with('user.department')->select('warning_letters.*');
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
            Column::make("Nama", "user.name")
                ->sortable()
                ->searchable(),
            Column::make("Department", "user.department.name")
                ->sortable()
                ->searchable(),
            Column::make("SP1", "sp1")
                ->sortable()
                ->searchable(),
            Column::make("SP2", "sp2")
                ->sortable()
                ->searchable(),
            Column::make("SP3", "sp3")
                ->sortable()
                ->searchable(),
            Column::make("Nilai", "nilai")
                ->sortable()
                ->searchable(),
            Column::make('Periode', 'periode')
                ->sortable()
                ->searchable()
                ->view('components.table.custom.format_date'),
        ];
        if (UserAccess::can('warning-letters', 'manage-warning-letters') || auth()->user()->is_superadmin) {
            $column[] = Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
                );
        }
        return $column;
    }
}
