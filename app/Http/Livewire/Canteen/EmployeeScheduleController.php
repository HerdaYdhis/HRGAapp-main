<?php

namespace App\Http\Livewire\Canteen;

use Livewire\WithFileUploads;
use App\Models\EmployeeSchedule;
use App\Models\User;
use App\Models\MealShift;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportEmployeeSchedule;
use App\Exports\ExportEmployeeScheduleExt;
use App\Imports\ImportEmployeeSchedule;
use App\Imports\ImportUpdateEmployeeSchedule;

class EmployeeScheduleController extends Component
{
    use WithFileUploads;
    public $modal = 'employee_modal';

    public $employee_id, $user_id, $meal_shift_id, $work_date, $start_date, $end_date, $type_download;

    public $is_edit = false;
    public $is_download = false;
    public $is_delete = false;

    public $is_upload_excel = false;
    public $is_update_excel = false;

    public $_id;

    public $employee, $userdata, $meal_shifts = [];
    public $excelFile, $type;
    public $selectedOption = null;

    protected $listeners = [
        'edit',
        'delete'
    ];

    protected $rules = [
        'user_id' => 'required',
        'work_date' => 'required'
    ];

    public function mount()
    {
        $this->userdata();
        $this->shifts();
        // dd($this->shifts());
    }

    public function userdata()
    {
        $userdata = User::get();
        return $this->userdata = $userdata;
    }
    public function shifts()
    {
        $meal_shifts = MealShift::get();
        $this->meal_shifts = $meal_shifts;
    }


    public function updateEmployeeId()
    {
        $this->employee_id = User::find($this->user_id)->employee_id;
    }

    public function render()
    {
        return view('livewire.canteen.employee-schedule')->layoutData([
            'title' => 'Employee Schedule'
        ]);
    }

    public function openModal()
    {
        $this->is_upload_excel = false;
        $this->is_edit = false;
        $this->is_download = false;
        $this->is_update_excel = false;
        $this->is_delete = false;
        $this->resetErrorBag();
        $this->reset_form();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function openModalExcel()
    {
        $this->is_upload_excel = true;
        $this->is_download = false;
        $this->is_edit = false;
        $this->is_update_excel = false;
        $this->is_delete = false;
        $this->resetErrorBag();
        $this->reset_form();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function openModalExcelUpdate()
    {
        $this->is_update_excel = true;
        $this->is_upload_excel = false;
        $this->is_download = false;
        $this->is_edit = false;
        $this->is_delete = false;
        $this->resetErrorBag();
        $this->reset_form();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }


    public function openModalDownload()
    {
        $this->is_upload_excel = false;
        $this->is_edit = false;
        $this->is_download = true;
        $this->is_update_excel = false;
        $this->is_delete = false;
        $this->resetErrorBag();
        $this->reset_form();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function deleteModal()
    {
        $this->is_upload_excel = false;
        $this->is_edit = false;
        $this->is_download = false;
        $this->is_update_excel = false;
        $this->is_delete = true;
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

    public function reset_form()
    {
        $this->work_date = null;
        $this->user_id = null;
        $this->meal_shift_id = null;
    }

    public function import()
    {

        $this->validate([
            'excelFile' => 'required|mimes:xls,xlsx',
        ]);

        if ($this->selectedOption === 'upload') {
            $import = new ImportEmployeeSchedule;
        } else {
            $import = new ImportUpdateEmployeeSchedule;
        }
        Excel::import($import, $this->excelFile);
        $this->emit('re_render_table');
        $this->closeModal();
        session()->flash('success', 'Successfully import employee schedule');
    }

    public function download()
    {
        $this->validate([
            'start_date' => 'required',
            'end_date' => 'required',
            'type_download' => 'required',
        ]);
        $start_date = $this->start_date;
        $end_date = $this->end_date;
        $type = $this->type_download;
        // $data = EmployeeSchedule::leftJoin('users', 'employee_schedules.user_id', '=', 'users.id')
        //     ->leftJoin('meal_shifts', 'employee_schedules.meal_shift_id', '=', 'meal_shifts.id')
        //     ->select('users.employee_id as employee_id', 'users.name as employee_name', 'meal_shifts.shift_name', 'employee_schedules.work_date')
        //     ->orderBy('employee_schedules.work_date', 'asc')
        //     ->get();
        $data = User::get();
        $this->closeModal();
        if ($type == 'template') {
            return Excel::download(new ExportEmployeeSchedule($data, $start_date, $end_date), 'Schedule.xlsx');
        } else {
            return Excel::download(new ExportEmployeeScheduleExt($data, $start_date, $end_date), 'ScheduleImport.xlsx');
        }
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        if ($this->is_edit) {
            $check = EmployeeSchedule::where('user_id', $this->user_id)->where('work_date', $this->work_date)->first();
            if ($check) {
                if ($check->meal_shift_id == $this->meal_shift_id) {
                    session()->flash('error', 'An error occurred : User data is already available');
                    $this->closeModal();
                } else {
                    $data = EmployeeSchedule::where('id', $this->_id)->update([
                        'user_id' => $this->user_id,
                        'employee_id' => $this->employee_id,
                        'meal_shift_id' => $this->meal_shift_id,
                        'work_date' => $this->work_date,
                    ]);
                    if ($data) {
                        $this->emit('re_render_table');
                        $this->closeModal();
                        session()->flash('success', 'Successfully save employee schedule');
                    } else {
                        session()->flash('error', 'Failed to save employee schedule');
                    }
                }
            } else {
                $data = EmployeeSchedule::where('id', $this->_id)->update([
                    'user_id' => $this->user_id,
                    'employee_id' => $this->employee_id,
                    'meal_shift_id' => $this->meal_shift_id,
                    'work_date' => $this->work_date,
                ]);
                if ($data) {
                    $this->emit('re_render_table');
                    $this->closeModal();
                    session()->flash('success', 'Successfully save employee schedule');
                } else {
                    session()->flash('error', 'Failed to save employee schedule');
                }
            }
        } else {
            $check = EmployeeSchedule::where('user_id', $this->user_id)->where('work_date', $this->work_date)->first();
            if ($check) {
                session()->flash('error', 'An error occurred : User data is already available');
                $this->closeModal();
            } else {
                $data = EmployeeSchedule::create([
                    'user_id' => $this->user_id,
                    'employee_id' => $this->employee_id,
                    'meal_shift_id' => $this->meal_shift_id,
                    'work_date' => $this->work_date,
                ]);
                if ($data) {
                    $this->emit('re_render_table');
                    $this->closeModal();
                    session()->flash('success', 'Successfully save employee schedule');
                } else {
                    session()->flash('error', 'Failed to save employee schedule');
                }
            }
        }
    }


    public function edit($data)
    {
        // dd($data);
        $this->resetErrorBag();
        $this->is_edit = true;
        $this->is_download = false;
        $this->is_upload_excel = false;
        $employee = EmployeeSchedule::where('id', $data['id'])->first();
        $this->_id = $employee['id'];
        $this->user_id = $employee['user_id'];
        $this->employee_id = $employee['employee_id'];
        $this->meal_shift_id = $employee['meal_shift_id'];
        $this->work_date = $employee['work_date'];
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = EmployeeSchedule::where('id', $data['id'])->delete();
        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully delete Meal Shift');
        } else {
            session()->flash('error', 'Failed to delete Meal Shift');
        }
    }

    public function delete_range()
    {
        // dd('hehe');
        $this->resetErrorBag();
        $start_date = $this->start_date;
        $end_date = $this->end_date;

        $deleted = EmployeeSchedule::whereBetween('work_date', [$start_date,  $end_date])->delete();

        if ($deleted) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully Delete Range');
        } else {
            session()->flash('error', 'Failed to Delete Range');
        }
    }
}
