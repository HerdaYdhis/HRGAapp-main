<?php

namespace App\Http\Livewire\Canteen;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Canteen;
use App\Models\Department;
use App\Models\MealShift;
use Livewire\Component;
use App\Exports\CanteenExport;
use App\Models\EmployeeSchedule;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Validator;
use App\Imports\ImportUpdateCanteen;
use Livewire\WithFileUploads;

class CanteenResult extends Component
{
    use WithFileUploads;
    public $employee_id;
    protected $listeners = [
        'scanEmployee',
        'delete',
        'edit'
    ];

    public $name, $employee, $eat, $department, $sum_day, $coa_id, $costcenter_id, $total = 0, $meal_time;
    public $total1, $total2, $totalpayout1 = 0, $totalpayout2 = 0;
    public $userdata, $userdepartment, $meal_shifts, $work_date, $meal_shift_id, $department_id, $user_id;

    public $modal = 'canteen_modal';
    public $is_edit = false;
    public $is_upload = false;
    public $_id;
    public $excelFile;

    public function mount()
    {

        $this->userdata = User::all();
        $this->userdepartment = Department::all();
        $this->meal_shifts = MealShift::get();
    }

    public function openModal()
    {
        $this->is_upload = false;
        $this->is_edit = false;
        $this->resetErrorBag();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function closeModal()
    {
        $this->resetErrorBag();
        $this->dispatchBrowserEvent('modal-hide', ['modal' => $this->modal]);
    }

    public function reset_form()
    {
        $this->user_id = null;
        $this->meal_shift_id = null;
        $this->work_date = null;
    }


    public function submit()
    {
        if ($this->is_edit) {
            $data = Canteen::where('id', $this->_id)->update([
                'eat' => $this->eat,
                'meal_shift_id' => $this->meal_shift_id,
            ]);
            $this->is_edit = false;
            if ($data) {
                $this->emit('re_render_table');
                $this->closeModal();
                session()->flash('success', 'Successfully update data');
            } else {
                session()->flash('error', 'Failed to update data');
            }
            $this->closeModal();
        } else {
            $cek = Canteen::where('employee_id', $this->user_id)->whereDate('created_at', $this->work_date)->first();
            // dd($cek);
            if ($cek !== null) {
                session()->flash('error', 'Data already exists');
                $this->closeModal();
            } else {
                $dep = User::where('employee_id', $this->user_id)->first();
                $data = Canteen::create([
                    'employee_id' => $this->user_id,
                    'eat' => 1,
                    'department_id' => $dep->department_id,
                    'meal_shift_id' => $this->meal_shift_id,
                    'created_at' => $this->work_date,
                ]);
                if ($data) {
                    $this->emit('re_render_table');
                    $this->closeModal();
                    session()->flash('success', 'Successfully update data');
                    $this->reset_form();
                } else {
                    session()->flash('error', 'Failed to update data');
                }
                $this->closeModal();
            }
        }
    }


    public function delete($data)
    {
        $this->resetErrorBag();

        $data = Canteen::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully delete Canteen');
        } else {
            session()->flash('error', 'Failed to saved Canteen');
        }
    }

    // ----------------------------------------------

    public function export()
    {
        $fileName = Carbon::now()->format('d-m-Y') . '_canteen.xlsx';
        return Excel::download(new CanteenExport, $fileName);
    }


    public function render()
    {
        return view('livewire.canteen.canteen-result')->layoutData([
            'title' => 'Canteen'
        ]);
    }

    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;
        $this->is_upload = false;
        $canteen = Canteen::where('id', $data['id'])->first();
        $this->_id = $canteen['id'];
        $this->eat = $canteen['eat'];
        $this->meal_shift_id = $canteen['meal_shift_id'];
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function import()
    {
        $this->validate([
            'excelFile' => 'required|mimes:xls,xlsx',
        ]);

        $import = new ImportUpdateCanteen;
        Excel::import($import, $this->excelFile);
        $this->emit('re_render_table');
        $this->closeModal();
        session()->flash('success', 'Successfully import canteen');
    }

    public function openModalExcel()
    {
        $this->is_upload = true;
        $this->is_edit = false;
        $this->resetErrorBag();
        $this->reset_form();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }
}
