<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use App\Utilities\UserAccess;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class Datatable extends DataTableComponent
{
    public function builder(): Builder
    {
        return User::query()
            // ->where('is_superadmin', false)
            ->with('role:id,name', 'department', 'userLevel', 'branch')->select('users.*');
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
        $column = [
            Column::make("Employee ID", "employee_id")
                ->sortable()
                ->searchable(),
            Column::make("Name", "name")
                ->sortable()
                ->searchable(),
            Column::make("Email", "email")
                ->sortable()
                ->searchable(),
            Column::make("Department", "department.name")
                ->sortable()
                ->searchable(),
            Column::make("User Level", "UserLevel.level")
                ->sortable()
                ->searchable(),
            Column::make("Status", "is_active")
                ->sortable()
                ->searchable()
                ->view('components.table.custom.customStatusEmployee'),
            Column::make("Hak Akses", "role.name")
                ->sortable()
                ->searchable(),
            Column::make("Branch", "branch.name")
            ->sortable()
                ->searchable(),
            Column::make("Status", "employee_status.name")
            ->sortable()
                ->searchable(),
            Column::make("Employee ID", "employee_id")
                ->format(function ($value, $column, $row) {
                    $encryptedEmployeeId = encrypt($value);
                    $qrCode = QrCode::size(200)->margin(1)->generate($encryptedEmployeeId);

                    // Extract SVG content and encode only the content
                    $svgContent = substr($qrCode, strpos($qrCode, '<svg'));
                    $base64EncodedSvg = 'data:image/svg+xml;base64,' . base64_encode($svgContent);

                    // Wrap the base64-encoded SVG in an <img> tag and use the returnHtml method
                    return new HtmlString("<img src='$base64EncodedSvg' width='200' height='200' />");
                })
                ->searchable(),



        ];
        if (UserAccess::can('category', 'manage-category') || auth()->user()->is_superadmin) {
            $column[] = Column::make("Aksi", "id")
                ->format(
                    fn ($value, $row, Column $column) => view('components.table.custom.action')->withRow($row),
                );
        }
        return $column;
    }
}
