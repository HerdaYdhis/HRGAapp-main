<?php

namespace App\Http\Livewire\StatusEmployee;

use App\Models\EmployeeStatus;
use Livewire\Component;

class Index extends Component
{
    public $modal = 'status-employee_modal';

    public $name;

    public $is_edit = false;

    public $_id;

    protected $listeners = [
        'edit',
        'delete'
    ];

    protected $rules = [
        'name' => 'required|string'
    ];

    // protected $messages = [
    //     'name.required' => 'This image cannot be empty.',
    //     'role.required' => 'This image cannot be empty.',
    // ];

    public function openModal()
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

    public function reset_form()
    {
        $this->name = null;
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        if ($this->is_edit) {
            $data = EmployeeStatus::where('id', $this->_id)->update([
                'name' => $this->name
            ]);
        } else {
            $data = EmployeeStatus::create([
                'name' => $this->name
            ]);
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved Status');
        } else {
            session()->flash('error', 'Failed to saved Status');
        }
    }


    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;

        $Status = EmployeeStatus::where('id', $data['id'])->first();
        $this->_id = $Status['id'];
        $this->name = $Status['name'];

        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = EmployeeStatus::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved Status');
        } else {
            session()->flash('error', 'Failed to saved Status');
        }
    }

    public function render()
    {
        return view('livewire.status-employee.index')->layoutData(['title' => 'Master Status Employee']);
    }
}
