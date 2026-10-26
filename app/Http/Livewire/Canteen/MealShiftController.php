<?php

namespace App\Http\Livewire\Canteen;

use App\Models\MealShift;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MealShiftController extends Component
{
    public $modal = 'mealshift_modal';

    public $shift_name, $monday_meal_time, $tuesday_meal_time, $wednesday_meal_time, $thursday_meal_time, $friday_meal_time, $saturday_meal_time, $sunday_meal_time;

    public $is_edit = false;

    public $_id;

    public $mealshifts;

    protected $listeners = [
        'edit',
        'delete'
    ];

    protected $rules = [
        'shift_name' => 'required'
    ];

    public function render()
    {
        return view('livewire.canteen.meal-shift')->layoutData([
            'title' => 'Meal Shift'
        ]);
    }

    public function openModal()
    {
        $this->is_edit = false;
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
        $this->shift_name = null;
        $this->monday_meal_time = null;
        $this->tuesday_meal_time = null;
        $this->wednesday_meal_time = null;
        $this->thursday_meal_time = null;
        $this->friday_meal_time = null;
        $this->saturday_meal_time = null;
        $this->sunday_meal_time = null;
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        if ($this->is_edit) {
            $data = MealShift::where('id', $this->_id)->update([
                'shift_name' => $this->shift_name,
                'monday_meal_time' => $this->monday_meal_time,
                'tuesday_meal_time' => $this->tuesday_meal_time,
                'wednesday_meal_time' => $this->wednesday_meal_time,
                'thursday_meal_time' => $this->thursday_meal_time,
                'friday_meal_time' => $this->friday_meal_time,
                'saturday_meal_time' => $this->saturday_meal_time,
                'sunday_meal_time' => $this->sunday_meal_time,
            ]);
        } else {
            $data = MealShift::create([
                'shift_name' => $this->shift_name,
                'monday_meal_time' => $this->monday_meal_time,
                'tuesday_meal_time' => $this->tuesday_meal_time,
                'wednesday_meal_time' => $this->wednesday_meal_time,
                'thursday_meal_time' => $this->thursday_meal_time,
                'friday_meal_time' => $this->friday_meal_time,
                'saturday_meal_time' => $this->saturday_meal_time,
                'sunday_meal_time' => $this->sunday_meal_time,
            ]);
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully save Meal Shift');
        } else {
            session()->flash('error', 'Failed to save Meal Shift');
        }
    }


    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;
        $mealshifts = MealShift::where('id', $data['id'])->first();
        $this->_id = $mealshifts['id'];
        $this->shift_name = $mealshifts['shift_name'];
        $this->monday_meal_time = $mealshifts['monday_meal_time'];
        $this->tuesday_meal_time = $mealshifts['tuesday_meal_time'];
        $this->wednesday_meal_time = $mealshifts['wednesday_meal_time'];
        $this->thursday_meal_time = $mealshifts['thursday_meal_time'];
        $this->friday_meal_time = $mealshifts['friday_meal_time'];
        $this->saturday_meal_time = $mealshifts['saturday_meal_time'];
        $this->sunday_meal_time = $mealshifts['sunday_meal_time'];
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = MealShift::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully delete Meal Shift');
        } else {
            session()->flash('error', 'Failed to delete Meal Shift');
        }
    }
}
