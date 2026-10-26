<?php

namespace App\Http\Livewire\Canteen;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\MealShift;

class DatatableMeal extends DataTableComponent
{
    protected $model = MealShift::class;

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
            Column::make("Shift Name", "shift_name")
                ->sortable()
                ->searchable(),
            Column::make("Monday Name", "monday_meal_time")
                ->sortable()
                ->searchable(),
            Column::make("Tuesday Name", "tuesday_meal_time")
                ->sortable()
                ->searchable(),
            Column::make("Wednesday Name", "wednesday_meal_time")
                ->sortable()
                ->searchable(),
            Column::make("Thursday Name", "thursday_meal_time")
                ->sortable()
                ->searchable(),
            Column::make("Friday Name", "friday_meal_time")
                ->sortable()
                ->searchable(),
            Column::make("Saturday Name", "saturday_meal_time")
                ->sortable()
                ->searchable(),
            Column::make("Sunday Name", "sunday_meal_time")
                ->sortable()
                ->searchable(),
            Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
                ),
        ];
    }
}
