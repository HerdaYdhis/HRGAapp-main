<?php

namespace App\Http\Livewire\Canteen;

use Carbon\Carbon;
use App\Models\Canteen;
use App\Exports\CanteenExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;


class DatatableScan extends DataTableComponent
{
    public function builder(): Builder
    {
        $today = Carbon::today()->toDateString();
        return Canteen::query()->with('user:id,name')
            // ->with('employeeschedule.meal_shifts')
            ->with('department:id,name')
            // ->with('meal_shifts')->select('canteens.*');
            ->with(['meal_shifts:id,shift_name,canteen_id']);
    }

    protected $listeners = [
        're_render_table' => '$refresh'
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
        $this->setEagerLoadAllRelationsEnabled();

        $this->setPerPage(10); // Default per page
        $this->setPerPageAccepted([10, 25, 50, 100]); // Opsi dropdown
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
                ->sortable(),
            Column::make("Shift", "meal_shifts.shift_name")
                ->sortable(),
            Column::make("Meal Time", "meal_shifts.{$currentDay}_meal_time")
                ->sortable(),
            Column::make("Scan Time", "scan_time")
                ->sortable(),
            Column::make("Department", "department.name")
                ->sortable(),
            Column::make("Employee id", "employee_id")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->format(function ($value) {
                    return Carbon::parse($value)->locale('id')->isoFormat('LL LT'); // 'LL' untuk tanggal, 'LT' untuk waktu
                })
                ->sortable()
                ->searchable(),

        ];


        if (auth()->user()->is_superadmin) {
            $column[] = Column::make("Aksi", "id")
                ->format(
                    fn($value, $row, Column $column) => view('components.table.custom.actionfull')->withRow($row),
                );
        }


        return $column;
    }
    public function filters(): array
    {
        return [
            DateFilter::make('Date')
                ->filter(function (Builder $builder, string $value) {
                    $builder->whereDate('canteens.created_at', '=', $value);
                }),

        ];
    }
}
