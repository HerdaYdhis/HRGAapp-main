<?php

namespace App\Http\Livewire\Task;

use DateTime;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\DetailTask;
use App\Utilities\UserAccess;
use Illuminate\Support\HtmlString;
use function PHPUnit\Framework\isNull;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class DatatableProject extends DataTableComponent
{
    public function builder(): Builder
    {
        return Task::query()->where('type', 'project')
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('name', auth()->user()->name);
            });
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

    function calculateRemainingTime($row)
    {
        $dueDate = new DateTime($row->duedate);
        $now = new DateTime();

        if ($dueDate > $now && $row->status == 'open') {
            $interval = $now->diff($dueDate);
            $format = '';
            if ($interval->d > 0 || $interval->h > 0 || $interval->i > 0) {
                $format .= '%d days ';
            }
            return $interval->format($format);
        } elseif ($dueDate < $now && $row->status == 'open') {
            $interval = $now->diff($dueDate);
            $format = '';
            if ($interval->d > 0 || $interval->h > 0 || $interval->i > 0) {
                $format .= '- %d days ';
            }
            return $interval->format($format);
        } elseif ($row->status == 'done') {
            return 'Close';
        }
    }

    function calculatePercentage($row)
    {
        $done = DetailTask::where('task_id', $row->id)->where('status', 'done')->count() ?? 0;
        $count = DetailTask::where('task_id', $row->id)->where('status', '<>', 'cancelled')->count() ?? 0;

        if ($count > 0) {
            $final = ($done / $count) * 100;
            return number_format($final, 2);
        } else {
            return 'N/A';
        }
    }


    public function columns(): array
    {
        $column = [
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
            Column::make("Due Date", "duedate")
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return Carbon::parse($value)->locale('id')->isoFormat('LL'); // 'LL' untuk tanggal
                }),

            Column::make("File", "image")
                ->format(function ($value, $row, Column $column) {
                    if ($value === null) {
                        // return new HtmlString('<img src="' . $value . '" alt="Null">');
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
                        default => 'bg-warning',
                    };
                    $badge = new HtmlString('<span class="badge ' . $badgeClass . '">' . $value . '</span>');
                    return $badge;
                }),
            Column::make('Remaining')
                ->label(function ($row, Column $column) {
                    return $this->calculateRemainingTime($row);
                }),
            Column::make('Percentage')
                ->label(function ($row) {
                    return view('components.table.custom.progress_bar')->withRow($this->calculatePercentage($row));
                }),
            BooleanColumn::make('Progressing', 'progress')
        ];

        // if (UserAccess::can('task', 'manage-task') || auth()->user()->is_superadmin) {


        // $column[] = Column::make("Aksi", "id")
        //     ->format(function ($value, $row, Column $column) {
        //         return view('components.table.custom.actionfull_task_project')
        //             ->with([
        //                 'row' => $row,
        //                 'showEditButton' => $row->progress,
        //                 'notShowDetail' => $row->progress == false,

        //                 // var_dump($row->progress)
        //             ]);
        //     });
        $column[] = Column::make("Aksi", "id")
            ->format(
                fn ($value, $row, Column $column) => view('components.table.custom.actionfull_task_project')->withRow($row),
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
