<?php

namespace App\Http\Livewire\ClosePeriode;

use Carbon\Carbon;
use App\Models\ClosePeriode;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class Datatable extends DataTableComponent
{
    public function builder(): Builder
    {
        return ClosePeriode::query()->with('user:id,name')
            ->select('close_periodes.*');
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
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Name", "user.name")
                ->sortable()
                ->searchable(),
            Column::make("Percentage (%)", "percentage")
                ->sortable()
                ->searchable(),
            Column::make("Month", "month")
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return Carbon::parse($value)->translatedFormat('F');
                }),
            Column::make("Created at", "created_at")
                ->sortable()
                ->searchable(),
            Column::make("Updated at", "updated_at")
                ->sortable()
                ->searchable(),
        ];
    }
}
