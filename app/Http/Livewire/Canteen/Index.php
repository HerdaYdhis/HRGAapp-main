<?php

namespace App\Http\Livewire\Canteen;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Canteen;
use Livewire\Component;
use App\Exports\CanteenExport;
use App\Models\EmployeeSchedule;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Encryption\DecryptException;

class Index extends Component
{
    public $employee_id;
    protected $listeners = [
        'scanEmployee',
        'delete',
        'edit'
    ];

    public $name, $employee, $eat, $department, $sum_day, $coa_id, $costcenter_id, $total = 0, $meal_time, $shift;
    public $total1, $total2, $totalpayout1 = 0, $totalpayout2 = 0;


    public $modal = 'canteen_modal';
    public $is_edit = false;
    public $is_delete = false;
    public $_id;

    public function openModal()
    {
        $this->resetErrorBag();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function DeleteModal()
    {
        $this->is_delete = true;
        $this->resetErrorBag();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function closeModal()
    {
        $this->resetErrorBag();
        $this->dispatchBrowserEvent('modal-hide', ['modal' => $this->modal]);
    }


    public function submit()
    {
        if ($this->is_delete) {
            $data = Canteen::whereDate('created_at', Carbon::today())->delete();
            if ($data) {
                $this->emit('re_render_table');
                $this->closeModal();
                session()->flash('success', 'Successfully Delete Bulk Canteen');
            } else {
                session()->flash('error', 'Failed to Delete Bulk Canteen');
            }

            $this->is_delete = false;
        } else {
            $this->resetErrorBag();
            $em_sche = EmployeeSchedule::whereDate('work_date', now()->toDateString())
                ->get();


            if ($em_sche->isEmpty()) {
                // $em_sche->count() === 0
                $this->closeModal();
                session()->flash('error', 'Scheduled Canteen is Empty');
            }

            foreach ($em_sche as $scheduled) {
                $user = User::where('employee_id', $scheduled->employee_id)->first();

                $existingeat = Canteen::where('employee_id', $scheduled->employee_id)
                    ->whereDate('created_at', now()->toDateString())
                    ->first();

                if (!$existingeat) {
                    // penetapan nilai awal untuk meal_shift -> shift_name
                    // 1. NS
                    // 2. dayoff
                    // 3. apapun yang bernilai awal 1 dianggap NS yang akan bernilai 0 pada eat_value pada canteen

                    // if ($scheduled->meal_shifts->shift_name != 'dayoff') {
                    // if (strpos($scheduled->meal_shifts->shift_name, '1') === 0) {
                    //     $eatValue = '0';
                    // } elseif ($scheduled->meal_shifts->shift_name === 'NS') {
                    //     $eatValue = '0';
                    // } else {
                    //     $eatValue = '1';
                    // }

                    $today = Carbon::now();
                    $dayOfWeek = $today->dayOfWeekIso; // Get the day of the week as a number (0 for Monday, 6 for Sunday)

                    // dd($dayOfWeek);
                    // $dayOfWeek = 5;

                    if ($dayOfWeek >= 6) {
                        if ($scheduled->meal_shifts->shift_name === 'dayoff') {
                            $eatValue = '0';
                        } else {
                            $eatValue = '1'; // Set $eatValue to 1 for Saturday or Sunday
                        }
                    } elseif (strpos($scheduled->meal_shifts->shift_name, '1') === 0) {
                        $eatValue = '0';
                    } elseif ($scheduled->meal_shifts->shift_name === 'NS' || $scheduled->meal_shifts->shift_name === 'dayoff') {
                        $eatValue = '0';
                    } else {
                        $eatValue = '1'; // Default value for other days
                    }

                    $data = DB::table('canteens')->insert([
                        'employee_id' => $scheduled->employee_id,
                        'eat' => $eatValue,
                        'meal_shift_id' => $scheduled->meal_shift_id,
                        'department_id' => $user->department_id ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if ($data) {
                        $this->emit('re_render_table');
                        $this->closeModal();
                        session()->flash('success', 'Successfully Bulk Canteen');
                    } else {
                        session()->flash('error', 'Failed to Bulk Canteen');
                    }
                    // }
                }
            }
        }

        // if ($data) {
        //     $this->emit('re_render_table');
        //     $this->closeModal();
        //     session()->flash('success', 'Successfully Bulk Canteen');
        // } else {
        //     session()->flash('error', 'Failed to Bulk Canteen');
        // }
        // $this->closeModal();
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



    public function scanEmployee($scannedData)
    {
        // dd(now()->toDateString());
        try {
            $this->resetErrorBag();

            $scannedData = decrypt($scannedData);

            $shift = Canteen::where('employee_id', $scannedData)->whereDate('created_at', now()->toDateString())->with('meal_shifts')->first();
            // dd($shift);

            $user = User::where('employee_id', $scannedData)->first();

            if (!$user) {
                $this->name = '';
                $this->eat = 'no';
                $this->department = '';
                $this->shift = '';
                $this->employee = $scannedData;
                session()->flash('error', 'Gagal scan, Barcode tidak dikenali: ' . $scannedData);
                $error = 'Gagal scan';
                $response = ['error' => $error];
                $this->emit('scanEmployeeResult', $response);
                return;
            }

            $user_canteen = Canteen::where('employee_id', $scannedData)->whereDate('created_at', now()->toDateString())->first();
            if (!$user_canteen) { // jika tidak terdaftar
                $this->name = $user->name;
                $this->eat = 'no';
                $this->department = $user->department->name;
                $this->shift = $shift->meal_shifts->shift_name;
                $this->employee = $scannedData;
                session()->flash('error', 'Gagal Scan, Pengguna tidak terdaftar di kantin pada hari ini');
                $error = 'Gagal Scan';
                $response = ['error' => $error];
                $this->emit('scanEmployeeResult', $response);
                return;
            }

            if ($user_canteen->meal_shift_id == '2') { // jika dayoff
                $this->name = $user->name;
                $this->eat = 'no';
                $this->department = $user->department->name;
                $this->shift = $shift->meal_shifts->shift_name;
                $this->employee = $scannedData;
                session()->flash('error', 'Gagal Scan, Pengguna libur pada hari ini');
                $error = 'Gagal Scan';
                $response = ['error' => $error];
                $this->emit('scanEmployeeResult', $response);
                return;
            }

            $this->name = $user->name;
            $this->eat = 'yes';
            $this->department = $user->department->name;
            $this->shift = $shift->meal_shifts->shift_name;
            $this->employee = $scannedData;

            $existingRecord = DB::table('canteens')->where('employee_id', $scannedData)->where('eat', true)->whereDate('created_at', now()->toDateString())->first();
            // dd($existingRecord);
            if (!$existingRecord) {
                DB::table('canteens')->where('employee_id', $scannedData)->whereDate('created_at', now()->toDateString())->update([
                    'eat' => true,
                    'scan_time' => now(),
                ]);

                $this->emit('re_render_table');
                session()->flash('success', 'Scan berhasil untuk ' . $user->name);
                $success = 'Scan berhasil';
                $response = ['success' => $success];
            } else {
                session()->flash('error', 'Gagal, User : ' . $user->name . ' , sudah melakukan scan pada hari ini / bukan Shift NS atau 1');
                $error = 'Scan gagal';
                $response = ['error' => $error];
            }
        } catch (DecryptException $e) {
            $this->name = '';
            $this->eat = 'no';
            $this->department = '';
            $this->shift = '';
            $this->employee = '';
            session()->flash('error', 'Gagal scan, Barcode tidak dikenali.');
            $error = 'Gagal scan';
            $response = ['error' => $error];
            $this->emit('scanEmployeeResult', $response);
            return;
        }

        $this->emit('scanEmployeeResult', $response);

        return response()->json($response);
    }


    public function render()
    {
        return view('livewire.canteen.index')->layoutData([
            'title' => 'Canteen'
        ]);
    }

    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;
        $canteen = Canteen::where('id', $data['id'])->first();
        $this->_id = $canteen['id'];
        $this->eat = $canteen['eat'];
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }
}
