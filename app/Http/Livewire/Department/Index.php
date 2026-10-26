<?php

namespace App\Http\Livewire\Department;

use App\Models\Department;
use Livewire\Component;

class Index extends Component
{
    public $modal = 'department_modal';

    public $name, $role = [];

    public $is_edit = false;

    public $_id;

    public $departments;

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

    // public function mount()
    // {
    //     $this->departments = Department::ROLE_ACCESS;
    // }

    public function render()
    {
        return view('livewire.department.index')->layoutData([
            'title' => 'Department'
        ]);
    }
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
            $data = Department::where('id', $this->_id)->update([
                'name' => $this->name
            ]);
        } else {
            $data = Department::create([
                'name' => $this->name
            ]);
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved Department');
        } else {
            session()->flash('error', 'Failed to saved Department');
        }
    }


    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;
        $departments = Department::where('id', $data['id'])->first();
        $this->_id = $departments['id'];
        $this->name = $departments['name'];

        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = Department::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved Department');
        } else {
            session()->flash('error', 'Failed to saved Department');
        }
    }
}
