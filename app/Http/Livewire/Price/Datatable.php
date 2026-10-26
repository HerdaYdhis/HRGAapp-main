<?php

namespace App\Http\Livewire\Price;

use App\Models\PriceCanteen;
use Illuminate\Support\HtmlString;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class Datatable extends DataTableComponent
{
    protected $model = PriceCanteen::class;

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
            Column::make("Nama Price", "name")
                ->sortable(),
            Column::make("Prices", "prices")
                ->sortable(),
            Column::make("Status", "status")
                ->sortable()
                ->format(function ($value, $row, Column $column) {
                    $badgeClass = match ($value) {
                        'active' => 'bg-success',
                        default => 'bg-black',
                    };
                    $badge = new HtmlString('<span class="badge ' . $badgeClass . '">' . $value . '</span>');
                    return $badge;
                }),
            Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
                ),
        ];
    }
}
