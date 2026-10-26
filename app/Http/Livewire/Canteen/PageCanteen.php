<?php

namespace App\Http\Livewire\Canteen;

use Livewire\Component;
use App\Models\Canteen;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportRekapCanteen;
use App\Exports\ExportRekapCanteenExt;
use Illuminate\Support\Facades\DB;

class PageCanteen extends Component
{
    public $modal = 'rekap_modal';

    public $start_date, $end_date, $branch_id;


    public function reset_form()
    {
        $this->start_date = null;
        $this->end_date = null;
    }
    public function openModalDownload()
    {
        $this->resetErrorBag();
        $this->reset_form();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function closeModal()
    {
        $this->resetErrorBag();
        $this->reset_form();
        $this->dispatchBrowserEvent('modal-hide', ['modal' => $this->modal]);
    }

    public function render()
    {
        return view('livewire.canteen.page-canteen')->layoutData([
            'title' => 'Canteen Page'
        ]);;
    }

    public function download()
    {
        $this->validate([
            'start_date' => 'required',
            'end_date' => 'required',
            // 'branch_id' => 'required',
        ]);
        $start_date = $this->start_date;
        $end_date = $this->end_date;
        $branch_id = (int)$this->branch_id;
        // dd($branch_id);
        // $data = Canteen::with(['user', 'department', 'meal_shifts'])->get();
        // $data = Canteen::with(['user', 'department', 'meal_shifts'])->get()->sortBy(function ($canteen) {
        //     return $canteen->user->branch_id;
        // });
        // $data = Canteen::with(['user' => function ($query) {
        //     $query->where('employee_id', '0000304');
        // }])
        // ->limit(20)
        // ->get();
        // dd($start_date, $end_date);

        // $data = Canteen::with(['user', 'department', 'meal_shifts'])->orderBy('employee_id', 'asc')->first();
        // $data = Canteen::with('meal_shifts')->first();
        // $data = Canteen::orderBy('employee_id', 'asc')->get();

        // dd($data);

        $data = Canteen::with(['user', 'department', 'meal_shifts'])
            ->whereHas('user')
            ->whereHas('department')
            ->whereHas('meal_shifts')
            ->whereDate('created_at', '>=', $this->start_date)
            ->whereDate('created_at', '<=', $this->end_date)
            ->orderBy('employee_id', 'asc')
            ->get();


        // dd($data);


        // $data = DB::table('canteens')
        //     ->select('canteens.employee_id as employee_id', 'users.name as user_name', 'departments.name as depart_name', 'canteens.created_at', 'canteens.eat as eat')
        //     ->leftJoin('users', 'canteens.employee_id', '=', 'users.employee_id')
        //     ->leftJoin('departments', 'departments.id', '=', 'users.department_id')
        //     ->where('users.branch_id', '=',  2)
        //     // ->limit(10)
        //     ->get();
        // dd($data);

        $this->closeModal();
        return Excel::download(new ExportRekapCanteen($data, $start_date, $end_date), 'Rekap_Kantin' . $start_date . '|' . $end_date . '.xlsx');
    }
}
