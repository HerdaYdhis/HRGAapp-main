<?php

namespace App\Http\Livewire\Borrowing;

use Carbon\Carbon;
use App\Utilities\UserAccess;
use App\Models\BorrowingRoom;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class Datatable extends DataTableComponent
{
    public function builder(): Builder
    {
        return BorrowingRoom::query()
            ->with(['user', 'meetroom'])->select('borrowing_rooms.*');
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

    public function calculateLeasedTime($row)
    {
        $startDate = Carbon::parse($row->start_date . ' ' . $row->start_time);
        $endDate = Carbon::parse($row->done_date . ' ' . $row->done_time);

        $diff = $endDate->diff($startDate);

        $totalDays = $diff->d;
        $totalHours = $diff->h;
        $totalMinutes = $diff->i;

        return "$totalDays Hari, $totalHours Jam, $totalMinutes Menit";
    }

    public function columns(): array
    {
        $column = [
            Column::make("Peminjam", "user.name")
                ->sortable()
                ->searchable(),
            Column::make("Tujuan", "objective")
                ->sortable()
                ->searchable(),
            Column::make("Waktu Mulai")
                ->sortable()
                ->searchable()
                ->label(function ($row, Column $column) {
                    $formattedDate = Carbon::parse($row->start_date)->format('d-M-Y');
                    return $formattedDate . ' ' . $row->start_time;
                }),
            Column::make("Waktu Selesai")
                ->sortable()
                ->searchable()
                ->label(function ($row, Column $column) {
                    $formattedDate = Carbon::parse($row->done_date)->format('d-M-Y');
                    return $formattedDate . ' ' . $row->done_time;
                }),
            // Column::make("Start Pinjam", "start_date")
            //     ->sortable()
            //     ->searchable(),
            // Column::make("Waktu Pinjam", "start_time")
            //     ->sortable()
            //     ->searchable(),
            // Column::make("Tanggal Selesai", "done_date")
            //     ->sortable()
            //     ->searchable(),
            // Column::make("Waktu Selesai", "done_time")
            //     ->sortable()
            //     ->searchable(),
            Column::make("R Meeting", "meetroom.name")
                ->sortable()
                ->searchable(),
            Column::make("Note", "note")
                ->sortable()
                ->searchable(),
            Column::make('Durasi')
                ->label(function ($row, Column $column) {
                    return $this->calculateleasedtime($row);
                }),

        ];


        $column[] = Column::make("Aksi", "id")
            ->format(
                fn($value, $row, Column $column) => view('components.table.custom.actionfull_borrow_meeting')->withRow($row),
            );

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
                    $builder->where('borrowing_rooms.start_date', '>=', $value); // Memberikan alias pada kolom 'created_at'
                }),
            DateFilter::make('Verified To')
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('borrowing_rooms.start_date', '<=', $value); // Memberikan alias pada kolom 'created_at'
                }),

        ];
    }
}
