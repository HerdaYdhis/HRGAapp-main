<?php

namespace App\Http\Livewire\Performances;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class DatatableUser extends DataTableComponent
{
    public function builder(): Builder
    {
        if (auth()->user()->is_superadmin) {
            return User::query()->with('userLevel')->select('users.*');
        } else {
            return User::query()->where('parent_id', auth()->user()->id)->with('userLevel')->select('users.*');
        }
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
            Column::make("Nama", "name")
                ->sortable()
                ->searchable(),
            Column::make("Aksi", "userLevel.tunjangan")
                ->sortable()
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.custom_goto')->withRow($row),
                ),
        ];
    }
}
