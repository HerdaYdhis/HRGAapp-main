<?php

namespace App\Http\Livewire\Evaluation;

use App\Models\Evaluation;
use App\Utilities\UserAccess;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class DatatableEvaluation extends DataTableComponent
{
    // protected $model = Evaluation::class;
    public $user_id;

    public function builder(): Builder
    {
        return Evaluation::query()->where('evaluations.user_id', session('sendUserId'))->where('evaluations.periode', 'LIKE', '%' . session('sendPeriode') . '%')->with('performance', 'user')->select('evaluations.*');
    }

    protected $listeners = [
        're_render_table' => '$refresh'
    ];


    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('performance_id', 'desc');
        $this->setEagerLoadAllRelationsEnabled();
    }

    public function columns(): array
    {

        $column =  [
            Column::make("Id", "performance_id")
                ->sortable(),
            // Column::make("Indikator", "performance.indikator")
            //     ->sortable()
            //     ->searchable(),
            Column::make("Nilai", "Nilai")
                ->sortable()
                ->searchable(),
            Column::make("Nilai Akhir", "nilai_akhir")
                ->sortable()
                ->searchable(),
        ];
        if (UserAccess::can('evaluation', 'manage-evaluation') || auth()->user()->is_superadmin) {
            $column[] = Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.actionEdit')->withRow($row),
                );
        }
        return $column;
    }
}
