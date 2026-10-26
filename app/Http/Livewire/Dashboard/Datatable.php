<?php

namespace App\Http\Livewire\Dashboard;

use DateTime;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class Datatable extends DataTableComponent
{
    public $nameOptions = [];

    public function builder(): Builder
    {

        $query = Task::query()->with('user')->where('users.department_id', auth()->user()->department_id);

        if (session()->has('selectedUserId')) {
            $query->where('user_id', session('selectedUserId'));
            $this->resetPage();
            $this->gotoPage(1);
        }

        return $query->select('tasks.*');
    }

    protected $listeners = [
        're_render_table' => '$refresh',
    ];

    public function mount()

    {
        $this->resetPage();
        $this->gotoPage(1);
        $this->nameOptions = User::where('department_id', auth()->user()->department_id)->pluck('name', 'name')->toArray();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
        $this->setEagerLoadAllRelationsEnabled();
        // $this->nameOptions = ;
    }


    function is_image($filename)
    {
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), $allowed_extensions);
    }

    function is_file($filename)
    {
        $allowed_extensions = ['pdf', 'xlx', 'xlxs', 'doc', 'docx', 'ppt', 'pptx'];
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

    public function columns(): array
    {
        $column =  [
            Column::make('User', 'user.name')
                ->searchable()
                ->sortable(),
            Column::make("Title", "title")
                ->sortable()
                ->searchable(),
            Column::make("Description", "description")
                ->sortable()
                ->searchable(),
            Column::make("Type", "type")
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
                    return $value ? Carbon::parse($value)->locale('id')->isoFormat('LL') : '';
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

        ];

        $column[] = Column::make("Aksi", "id")
            ->format(
                fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
            );
        // }
        return $column;
    }
    public function filters(): array
    {
        return [

            // SelectFilter::make('User Name')
            //     ->options(['' => 'Select'] + $this->nameOptions)
            //     ->filter(function (Builder $builder, $value) {
            //         if (empty($value)) {
            //             return;
            //         }
            //         // Debug the selected value
            //         // dd($value);
            //         $builder->whereHas('user', function ($query) use ($value) {
            //         // Debug the generated SQL query
            //         // dd($query->toSql());

            //         $query->where('name', $value)->where('department_id', auth()->user()->department_id);
            //         });
            //     }),


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
                ])
                ->filter(function (Builder $builder, $value) {
                    $builder->where('tasks.status', $value);
                }),
            SelectFilter::make('Type')
                ->options([
                    '' => 'Select',
                    'daily' => 'Daily',
                    'monthly' => 'Monthly',
                    'project' => 'Project',

                ])
                ->filter(function (Builder $builder, $value) {
                    $builder->where('tasks.type', $value);
                }),



        ];
    }
}
