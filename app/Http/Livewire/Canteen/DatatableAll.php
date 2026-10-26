<?php

namespace App\Http\Livewire\Canteen;

use Carbon\Carbon;
use App\Models\User;
use App\Models\CanteenOutput;
use Illuminate\Support\Facades\DB;
use App\Exports\CanteenOutputExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class DatatableAll extends DataTableComponent
{

    public function builder(): Builder
    {
        return CanteenOutput::query()->with('user:id,name,coa_id,costcenter_id')->with('department:id,name')->with('coa:id,name')->select('canteen_outputs.*');
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
        $columns = [
            Column::make("Id", "id")->sortable(),
            Column::make("Employee id", "employee_id")->sortable()->searchable(),
            Column::make("Name", "user.name")->sortable()->searchable(),
            Column::make("Department", "department.name")->sortable()->searchable(),
        ];


        $firstDayOfMonth = now()->firstOfMonth();
        $fourteenthDayOfMonth = now()->firstOfMonth()->addDays(15);

        if (now() >= $firstDayOfMonth && now() <= $fourteenthDayOfMonth) {
            for ($i = 1; $i <= 15; $i++) {
                $columns[] = BooleanColumn::make("$i", "$i");
            }
            $columns[] = Column::make("Total1", "total1")->sortable()->searchable();
            $columns[] = Column::make("Total Payout1", "totalpayout1")->sortable()->searchable();
        } else {
            if (now()->endOfMonth()->format('d') == 31) {
                for ($i = 16; $i <= 31; $i++) {
                    $columns[] = BooleanColumn::make("$i", "$i");
                }
            } else {
                for ($i = 16; $i <= 30; $i++) {
                    $columns[] = BooleanColumn::make("$i", "$i");
                }
            }


            $columns[] = Column::make("Total2", "total2")->sortable()->searchable();
            $columns[] = Column::make("Total Payout2", "totalpayout2")->sortable()->searchable();
        }


        $columns[] = Column::make("COA", "coa.name")->sortable()->searchable();
        $columns[] = Column::make("Cost Center", "costcenter.name")->sortable()->searchable();
        $columns[] = Column::make("Status", "user.status_employee")->sortable()->searchable();

        $columns[] = Column::make("Created at", "created_at")
        ->format(function ($value) {
            return Carbon::parse($value)->locale('id')->isoFormat('LL LT'); // 'LL' untuk tanggal, 'LT' untuk waktu
        })->sortable()->searchable();

        return $columns;
    }

    public function bulkActions(): array
    {
        return [
            'export' => 'Export',
        ];
    }

    public function export()
    {
        $fileName = Carbon::now()->format('d-m-Y') . '_outputcanteen.xlsx';
        return Excel::download(new CanteenOutputExport, $fileName);
    }
}
