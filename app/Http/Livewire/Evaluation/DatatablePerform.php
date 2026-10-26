<?php

namespace App\Http\Livewire\Evaluation;

use App\Models\Evaluation;
use App\Models\Performance;
use App\Models\User;
use App\Utilities\UserAccess;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class DatatablePerform extends DataTableComponent
{
    public function builder(): Builder
    {
        return Performance::query()->where('user_id', session('sendUserId'))->with('evaluation')->select('performances.*');
        // dd($test);
    }

    // public function builder(): Builder
    // {
    //     return Evaluation::query()->with('performance')->select('evaluations.*');
    // }

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
            Column::make("Indikator", "indikator")
                ->sortable()
                ->searchable(),
            Column::make('bobot')
                ->sortable()
                ->footer(fn ($rows) => 'Total Bobot : ' . $rows->sum('bobot')),
            Column::make("Nilai")
                ->searchable()
                ->sortable()
                ->format(function ($value, $column, $row) {
                    return $row->evaluations->pluck('nilai')->implode(', ');
                }),
        ];
        if (UserAccess::can('evaluation', 'manage-evaluation') || auth()->user()->is_superadmin) {
            $column[] = Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.goto')->withRow($row),
                );
        }
        return $column;

        // return [
        //     Column::make('ID')->sortable(),
        //     Column::make('Indikator')->sortable(),
        //     Column::make('Bobot')->sortable(),
        //     Column::make('Evaluations', 'evaluations.nilai')
        //         ->searchable()
        //         ->sortable(),
        // ];
    }
}
