<?php

namespace App\Http\Livewire\Canteen;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\EmployeeSchedule;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;

class DatatableEmployee extends DataTableComponent
{
    // protected $model = EmployeeSchedule::class;

    public function builder(): Builder
    {
        return EmployeeSchedule::query()
            ->with(['user', 'meal_shifts'])
            ->select('employee_schedules.*');
    }

    protected $listeners = [
        're_render_table' => '$refresh'
    ];
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('work_date', 'desc');
        $this->setEagerLoadAllRelationsEnabled();
    }

    public function columns(): array
    {
        return [
            Column::make("Employee ID", "employee_id")
                ->sortable()
                ->searchable(),
            Column::make("Employee Name", "user.name")
                ->sortable()
                ->searchable(),
            Column::make("Shift", "meal_shifts.shift_name")
                ->sortable()
                ->searchable()
                ->view('components.table.custom.format_shift_dayoff'),
            Column::make("Work Date", "work_date")
                ->sortable()
                ->searchable(),
            Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
                ),
        ];
    }

    public function filters(): array
    {
        return [
            DateFilter::make('Date')
                ->filter(function (Builder $builder, string $value) {
                    $builder->whereDate('employee_schedules.work_date', '=', $value);
                }),

        ];
    }
}
