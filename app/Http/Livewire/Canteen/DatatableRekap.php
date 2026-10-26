<?php

namespace App\Http\Livewire\Canteen;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Canteen;
use Illuminate\Support\Facades\DB;
use App\Exports\CanteenOutputExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class DatatableRekap extends DataTableComponent
{
    public function builder(): Builder
    {
        return Canteen::query()->with('user:id,name')
            // ->with('employeeschedule.meal_shifts')
            ->with('department:id,name')
            ->with('meal_shifts')->select('canteens.*');
    }

    protected $listeners = [
        're_render_table' => '$refresh'
    ];

    public function configure(): void
    {
        $this->setFiltersStatus(true);
        $this->setPrimaryKey('id');
        $this->setDefaultSort('created_at', 'desc');
        $this->setEagerLoadAllRelationsEnabled();
    }

    public function columns(): array
    {
        $currentDay = strtolower(Carbon::now()->format('l'));
        $column = [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Name", "user.name")
                ->sortable()
                ->searchable(),
            BooleanColumn::make("Eat Status", "eat")
                ->sortable()
                ->searchable(),
            Column::make("Shift", "meal_shifts.shift_name")
                ->sortable()
                ->searchable(),
            Column::make("Meal Time", "meal_shifts.{$currentDay}_meal_time")
                ->sortable()
                ->searchable(),
            Column::make("Scan Time", "scan_time")
                ->sortable()
                ->searchable(),
            Column::make("Department", "department.name")
                ->sortable()
                ->searchable(),
            Column::make("Employee id", "employee_id")
                ->sortable()
                ->searchable(),
            Column::make("Created at", "created_at")
                ->format(function ($value) {
                    return Carbon::parse($value)->locale('id')->isoFormat('LL LT'); // 'LL' untuk tanggal, 'LT' untuk waktu
                })
                ->sortable()
                ->searchable(),

        ];


        // if (auth()->user()->is_superadmin) {
        //     $column[] = Column::make("Aksi", "id")
        //         ->format(
        //             fn ($value, $row, Column $column) => view('components.table.custom.action-delete')->withRow($row),
        //         );
        // }


        return $column;
    }
    public function filters(): array
    {
        return [
            DateFilter::make('Start Date')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('canteens.created_at', '>=', $value); // Memberikan alias pada kolom 'created_at'
                }),
            DateFilter::make('End Date')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('canteens.created_at', '<=', $value); // Memberikan alias pada kolom 'created_at'
                })
        ];
    }
}
