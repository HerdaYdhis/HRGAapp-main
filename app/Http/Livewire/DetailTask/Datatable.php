<?php

namespace App\Http\Livewire\DetailTask;

use App\Models\DetailTask;
use DateTime;
use Carbon\Carbon;
use App\Models\Task;
use App\Utilities\UserAccess;
use Illuminate\Support\HtmlString;
use function PHPUnit\Framework\isNull;
use Illuminate\Database\Eloquent\Builder;

use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class Datatable extends DataTableComponent
{
    public function builder(): Builder
    {
        return  DetailTask::query()->where('task_id', session('sendParameterTask'))
            ->whereHas('task.user', function ($query) {
                $query->where('name', auth()->user()->name);
            })
            ->with('task.user');
        // return DetailTask::query()->where('task_id', session('sendParameterTask'));
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

        // Check if the due date is in the future
        if ($dueDate > $now && $row->status == 'open') {
            $interval = $now->diff($dueDate);
            $format = '';
            if ($interval->d > 0) $format .= '%d days ';
            return $interval->format($format);
        } elseif ($dueDate < $now && $row->status == 'open') {
            $interval = $now->diff($dueDate);
            $format = 'Overdue ';
            if ($interval->d > 0) $format .= '%d days ';
            return $interval->format($format);
        } elseif ($row->status == 'done') {
            return 'Close';
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
            Column::make("Task", "task.title")
                ->sortable()
                ->searchable(),
            Column::make("User", "task.user.name")
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
            // Column::make("Due Date", "duedate")
            //     ->sortable()
            //     ->searchable()
            //     ->format(function ($value) {
            //         return Carbon::parse($value)->locale('id')->isoFormat('LL'); // 'LL' untuk tanggal
            //     }),

            // Column::make("File", "image")
            //     ->format(function ($value, $row, Column $column) {
            //         if ($value === null) {
            //             // return new HtmlString('<img src="' . $value . '" alt="Null">');
            //             return $value;
            //         } elseif ($this->is_image($value)) {
            //             return view('components.table.custom.image')->withRow($row);
            //         } elseif ($this->is_file($value)) {
            //             return view('components.table.custom.file')->withValue($value);
            //         }
            //     })
            //     ->sortable(),
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
            // Column::make('Remaining')
            //     ->label(function ($row, Column $column) {
            //         return $this->calculateRemainingTime($row);
            //     }),
        ];


        // if (UserAccess::can('task', 'manage-task') || auth()->user()->is_superadmin) {
        $column[] = Column::make("Aksi", "id")
            ->format(
                fn ($value, $row, Column $column) => view('components.table.custom.actionfull')->withRow($row),
            );
        // }
        return $column;
    }
}
