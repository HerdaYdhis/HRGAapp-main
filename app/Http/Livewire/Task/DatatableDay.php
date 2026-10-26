<?php

namespace App\Http\Livewire\Task;

use Carbon\Carbon;
use App\Models\Task;
use App\Utilities\UserAccess;
use Illuminate\Support\HtmlString;
use function PHPUnit\Framework\isNull;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class DatatableDay extends DataTableComponent
{
    public function builder(): Builder
    {
        return Task::query()
            ->where('type', 'daily')
            ->where('user_id', auth()->user()->id)
            ->with('user')
            ->select('tasks.*');
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

    function is_image($filename)
    {
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), $allowed_extensions);
    }

    function is_file($filename)
    {
        $allowed_extensions = ['pdf', 'xls', 'xlxs', 'doc', 'docx', 'ppt', 'pptx'];
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), $allowed_extensions);
    }


    public function columns(): array
    {

        $column = [
            // Column::make("Id", "id")
            //     ->sortable(),
            Column::make("Title", "title")
                ->sortable()
                ->searchable(),
            Column::make("Description", "description")
                ->sortable()
                ->searchable(),
            Column::make("User", "user.name")
                ->sortable()
                ->searchable(),
            Column::make("Updated", "updated_at")
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return Carbon::parse($value)->locale('id')->isoFormat('LL LT'); // 'LL' untuk tanggal, 'LT' untuk waktu
                }),
            Column::make("Created", "created_at")
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return Carbon::parse($value)->locale('id')->isoFormat('LL LT'); // 'LL' untuk tanggal, 'LT' untuk waktu
                }),
            Column::make("File", "image")
                ->format(function ($value, $row, Column $column) {
                    if ($value === null) {
                        return $value;
                    } elseif ($this->is_image($value)) {
                        return view('components.table.custom.image')->withRow($row);
                    } elseif ($this->is_file($value)) {
                        return view('components.table.custom.file')->withValue($value);
                    }
                })
                ->sortable(),
            Column::make("status", "status")
                ->sortable()
                ->searchable()
                ->format(function ($value, $row, Column $column) {
                    $badgeClass = match ($value) {
                        'done' => 'bg-success',
                    'done with overdue' => 'bg-danger',
                    'cancelled' => 'bg-black',
                    default => 'bg-warning',                    };
                    $badge = new HtmlString('<span class="badge ' . $badgeClass . '">' . $value . '</span>');
                    return $badge;
                }),
        ];

        // if (UserAccess::can('task', 'manage-task') || auth()->user()->is_superadmin) {
        $column[] = Column::make("Aksi", "id")
            ->format(
                fn ($value, $row, Column $column) => view('components.table.custom.actionfull')->withRow($row),
            );
        // }
        return $column;
    }

    public function filters(): array
    {
        return [

            DateFilter::make('Verified From')
                // ->config([
                //     'min' => '2020-01-01',
                //     'max' => '2021-12-31',
                // ])

                ->filter(function (Builder $builder, string $value) {
                    $builder->where('tasks.created_at', '>=', $value); // Memberikan alias pada kolom 'created_at'
                }),
            DateFilter::make('Verified To')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('tasks.created_at', '<=', $value); // Memberikan alias pada kolom 'created_at'
                }),
            SelectFilter::make('Status')
                ->options([
                    '' => 'Select',
                    'done' => 'Done',
                    'open' => 'Open',
                    'cancelled' => 'Cancel',
                ])
                ->filter(function (Builder $builder, $value) {
                    $builder->where('tasks.status', $value);
                }),

        ];
    }

}
