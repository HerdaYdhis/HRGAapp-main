<?php

namespace App\Http\Livewire\EvaluationResult;

use App\Models\EvaluationResult;
use App\Models\Performance;
use App\Models\User;
use App\Utilities\UserAccess;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class DatatableSelf extends DataTableComponent
{
    // protected $model = Performance::class;

    public function builder(): Builder
    {
        /** @var Builder $query */
        $query = EvaluationResult::query()
            ->where('user_id', auth()->user()->id)
            ->with(['user.department', 'user.userLevel'])
            ->select('evaluation_results.*');

        return $query;
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
            Column::make("ID Karyawan", "user.employee_id")
                ->sortable(),
            Column::make("Periode", "periode")
                ->view('components.table.custom.format_date')
                ->sortable()
                ->searchable(),
            Column::make("Nama", "user.name")
                ->sortable()
                ->searchable()
                ->sortable(),
            Column::make("Departemen", "user.department.name")
                ->sortable()
                ->searchable(),
            Column::make("Level Job", "user.userLevel.level")
                ->sortable()
                ->searchable(),
            Column::make("Nilai Kinerja", "nilai_kinerja")
                ->sortable()
                ->searchable(),
            Column::make("Nilai Akhir Kinerja", "nilai_akhir_kinerja")
                ->sortable()
                ->searchable(),
            Column::make("Nilai Kedisiplinan", "nilai_kedisiplinan")
                ->sortable()
                ->searchable(),
            Column::make("Nilai Akhir Kedisiplinan", "nilai_akhir_kedisiplinan")
                ->sortable()
                ->searchable(),
            Column::make("Total", "total")
                ->sortable()
                ->searchable(),
            Column::make("Persentase", "persentase")
                ->view('components.table.custom.percentage')
                ->sortable()
                ->searchable(),
            Column::make("Total SP", "nilai_sp")
                ->sortable()
                ->searchable(),
            Column::make("Total Nilai Akhir(%)", "total_akhir")
                ->view('components.table.custom.percentage')
                ->sortable()
                ->searchable(),
            Column::make("Nominal Tunjangan Yang Didapatkan
                (Rp)", "nominal")
                ->view('components.table.custom.custom_currency')
                ->sortable()
                ->searchable(),
        ];
        return $column;
    }
}
