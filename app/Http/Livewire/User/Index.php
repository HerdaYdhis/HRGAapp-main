<?php

namespace App\Http\Livewire\User;

use App\Models\Branch;
use App\Models\EmployeeSchedule;
use App\Models\MealShift;
use App\Models\Role;
use App\Models\Coa;
use App\Models\CostCenter;
use App\Models\User;
use App\Models\Department;
use App\Models\EmployeeStatus;
use App\Models\UserLevel;
use Livewire\Component;
use Throwable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class Index extends Component
{
    public $modal = 'user_modal';

    /** @var string|null */
    public $name;
    /** @var string|null */
    public $password;
    /** @var int|string|null */
    public $role_id;
    /** @var string|null */
    public $email;
    /** @var int|string|null */
    public $parent_id;
    /** @var int|string|null */
    public $department_id;
    /** @var int|string|null */
    public $user_level_id;
    /** @var int|string|null */
    public $coa_id;
    /** @var int|string|null */
    public $cost_center_id;
    /** @var int|string|bool|null */
    public $is_active;
    /** @var int|string|null */
    public $branch_id;
    /** @var int|string|null */
    public $status_employee_id;
    /** @var int|string|null */
    public $employee_id;

    public $is_edit = false;

    /** @var int|string|null */
    public $_id;

    /** @var mixed */
    public $roles;
    /** @var mixed */
    public $coa;
    /** @var mixed */
    public $cost_center;
    /** @var mixed */
    public $userdata;
    /** @var mixed */
    public $userdepartment;
    /** @var mixed */
    public $userlevel;
    /** @var mixed */
    public $employee_status;
    /** @var mixed */
    public $branch;

    protected $listeners = [
        'edit',
        'delete'
    ];

    protected $rules = [
        'name' => 'required|string',
        'is_active' => 'required',
    ];

    protected $messages = [
        'name.required' => 'Nama wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah terdaftar.',
        'employee_id.required' => 'Employee ID wajib diisi.',
        'employee_id.unique' => 'Employee ID sudah terdaftar.',
        'role_id.required' => 'Hak Akses wajib dipilih.',
        'role_id.exists' => 'Hak Akses tidak valid.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'is_active.required' => 'Status aktif wajib dipilih.',
    ];

    public function rules()
    {
        $additionalRules = [
            'employee_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'employee_id')->ignore($this->_id),
            ],
            'email' => 'required|email|unique:users,email,' . $this->_id,
            'role_id' => 'required|exists:roles,id',
        ];

        if (!$this->is_edit) {
            $additionalRules['password'] = 'required|min:6';
        }

        return array_merge($this->rules, $additionalRules);
    }

    public function mount()
    {
        $this->roles = Role::select('id', 'name')->get();
        $this->coa = Coa::get();
        $this->cost_center = CostCenter::get();
        $this->userdata = User::all();
        $this->userdepartment = Department::all();
        $this->userlevel = UserLevel::get();
        $this->branch = Branch::get();
        $this->employee_status = EmployeeStatus::get();
    }

    public function render()
    {
        return view('livewire.user.index')->layoutData([
            'title' => 'Pengguna'
        ]);
    }

    public function openModal()
    {
        $this->is_edit = false;
        $this->resetErrorBag();
        $this->reset_form();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function reset_form()
    {
        $this->name = null;
        $this->password = null;
        $this->_id = null;
        $this->email = null;
        $this->role_id = null;
        $this->department_id = null;
        $this->parent_id = null;
        $this->user_level_id = null;
        $this->coa_id = null;
        $this->cost_center_id = null;
        $this->is_active = 1;
        $this->status_employee_id = null;
        $this->branch_id = null;
        $this->employee_id = null;
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        try {
            $role_id = !empty($this->role_id) ? (int)$this->role_id : null;
            $department_id = !empty($this->department_id) ? (int)$this->department_id : null;
            $parent_id = !empty($this->parent_id) ? (int)$this->parent_id : null;
            $user_level_id = !empty($this->user_level_id) ? (int)$this->user_level_id : null;
            $coa_id = !empty($this->coa_id) ? (int)$this->coa_id : null;
            $cost_center_id = !empty($this->cost_center_id) ? (int)$this->cost_center_id : null;
            $branch_id = !empty($this->branch_id) ? (int)$this->branch_id : null;
            $status_employee_id = !empty($this->status_employee_id) ? (int)$this->status_employee_id : null;
            $is_active = ($this->is_active !== null && $this->is_active !== '') ? (int)$this->is_active : 1;

            if ($this->is_edit) {
                if ($is_active == 0) {
                    $dayoff = MealShift::where('shift_name', 'dayoff')->first();
                    if (!$dayoff) {
                        session()->flash('error', 'dayoff pada Master Data -> Meal Shift tidak tersedia');
                        $this->closeModal();
                        return;
                    }
                    EmployeeSchedule::where('user_id', $this->_id)
                        ->whereDate('work_date', '>=', now()->toDateString())
                        ->update([
                            'meal_shift_id' => $dayoff->id,
                        ]);
                }
                $updateData = [
                    'employee_id' => $this->employee_id,
                    'name' => $this->name,
                    'email' => $this->email,
                    'role_id' => $role_id,
                    'department_id' => $department_id,
                    'parent_id' => $parent_id,
                    'user_level_id' => $user_level_id,
                    'coa_id' => $coa_id,
                    'costcenter_id' => $cost_center_id,
                    'is_active' => $is_active,
                    'status_employee_id' => $status_employee_id,
                    'branch_id' => $branch_id,
                ];
                if ($this->password) {
                    $updateData['password'] = Hash::make($this->password);
                }
                $data = User::where('id', $this->_id)->update($updateData);
            } else {
                $data = User::create([
                    'employee_id' => $this->employee_id,
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'role_id' => $role_id,
                    'department_id' => $department_id,
                    'parent_id' => $parent_id,
                    'user_level_id' => $user_level_id,
                    'coa_id' => $coa_id,
                    'costcenter_id' => $cost_center_id,
                    'is_active' => $is_active,
                    'status_employee_id' => $status_employee_id,
                    'branch_id' => $branch_id,
                ]);

                // penetapan nilai awal untuk meal_shift -> shift_name
                $meal_shift = MealShift::where('shift_name', 'NS')->first();
                $dayoff = MealShift::where('shift_name', 'dayoff')->first();

                if ($meal_shift && $dayoff) {
                    $startDate = \Carbon\Carbon::now()->startOfDay();
                    $maxDate = EmployeeSchedule::max('work_date');
                    $lastDate = $maxDate ? \Carbon\Carbon::parse($maxDate)->startOfDay() : $startDate;
                    $daysDifference = $startDate->diffInDays($lastDate);
                    for ($day = 0; $day <= $daysDifference; $day++) {
                        $currentDate = $startDate->copy()->addDays($day);
                        $mealShiftId = ($currentDate->dayOfWeek == \Carbon\CarbonInterface::SATURDAY || $currentDate->dayOfWeek == \Carbon\CarbonInterface::SUNDAY)
                            ? $dayoff->id
                            : $meal_shift->id;

                        EmployeeSchedule::create([
                            'user_id' => $data->id,
                            'employee_id' => $data->employee_id,
                            'meal_shift_id' => $mealShiftId,
                            'work_date' => $currentDate->format('Y-m-d'),
                        ]);
                    }
                }
            }

            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Berhasil menyimpan pengguna');
        } catch (Throwable $e) {
            session()->flash('error', 'Gagal menyimpan pengguna: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->resetErrorBag();
        $this->reset_form();
        $this->dispatchBrowserEvent('modal-hide', ['modal' => $this->modal]);
    }

    public function edit(array $data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;
        $this->employee_id = $data['employee_id'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->role_id = $data['role_id'];
        $this->department_id = $data['department_id'];
        $this->user_level_id = $data['user_level_id'];
        $this->parent_id = $data['parent_id'];
        $this->coa_id = $data['coa_id'];
        $this->cost_center_id = $data['costcenter_id'];
        $this->is_active = $data['is_active'];
        $this->status_employee_id = $data['status_employee_id'];
        $this->branch_id = $data['branch_id'];
        $this->_id = $data['id'];

        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete(array $data)
    {
        $this->resetErrorBag();

        $data = User::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Berhasil menghapus pengguna');
        } else {
            session()->flash('error', 'Gagal menghapus pengguna');
        }
    }
}
