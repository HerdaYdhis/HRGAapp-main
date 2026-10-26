<?php

namespace App\Http\Livewire\Discipline;

use App\Models\Discipline;
use App\Utilities\UserAccess;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class Datatable extends DataTableComponent
{
    // protected $model = Evaluation::class;

    public function builder(): Builder
    {
        // return Discipline::query()->with('user')->select('disciplines.*');
        return Discipline::query()
            ->with('user.department')
            ->select('disciplines.*');
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
            Column::make("Nama", "user.name")
                ->sortable()
                ->searchable(),
            Column::make("Departemen", "user.department.name")
                ->sortable()
                ->searchable(),
            Column::make("Alfa", "alfa")
                ->sortable()
                ->searchable(),
            Column::make("Izin", "izin")
                ->sortable()
                ->searchable(),
            Column::make("Sakit", "sakit")
                ->sortable()
                ->searchable(),
            Column::make("Terlambat (≥5≤20 menit)", "terlambat1")
                ->sortable()
                ->searchable(),
            Column::make("Terlambat (>20 ≤60 menit)", "terlambat2")
                ->sortable()
                ->searchable(),
            Column::make("Terlambat (>60 menit)", "terlambat3")
                ->sortable()
                ->searchable(),
            Column::make("Nilai Kedisiplinan", "nilai_kedisiplinan")
                ->sortable()
                ->searchable(),
            Column::make("Nilai Akhir", "nilai")
                ->sortable()
                ->searchable(),
            // Column::make("Periode", "periode")
            //     ->sortable()
            //     ->searchable(),
            Column::make('Periode', 'periode')
                ->sortable()
                ->searchable()
                ->view('components.table.custom.format_date'),
        ];
        if (UserAccess::can('discipline', 'manage-discipline') || auth()->user()->is_superadmin) {
            $column[] = Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
                );
        }
        return $column;
    }
}
