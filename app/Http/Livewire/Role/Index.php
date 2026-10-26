<?php

namespace App\Http\Livewire\Role;

use App\Models\Role;
use Livewire\Component;

class Index extends Component
{
    public $modal = 'role_modal';

    public $name, $role = [];

    public $is_edit = false;

    public $_id;

    public $roles;

    protected $listeners = [
        'edit',
        'delete'
    ];

    protected $rules = [
        'name' => 'required|string',
        'role' => 'array',
    ];

    // protected $messages = [
    //     'name.required' => 'This image cannot be empty.',
    //     'role.required' => 'This image cannot be empty.',
    // ];

    public function mount()
    {
        $this->roles = Role::ROLE_ACCESS;
    }
    public function render()
    {
        return view('livewire.role.index')->layoutData([
            'title' => 'Hak Akses'
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
        $this->role = null;
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        if ($this->is_edit) {
            $data = Role::where('id', $this->_id)->update([
                'name' => $this->name,
                'menu_access' => $this->fixMenu($this->role),
            ]);
        } else {
            $data = Role::create([
                'name' => $this->name,
                'menu_access' => $this->fixMenu($this->role),
            ]);
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved access right');
        } else {
            session()->flash('error', 'failed to saved access right ');
        }
    }

    public function fixMenu($menu_access)
    {
        $return = [];
        $fix = collect($menu_access)->map(function ($item) {
            return collect($item)->filter(function ($item, $value) {
                return $item;
            })->toArray();
        })->toArray();
        foreach ($fix as $key => $value) {
            if (count($value) > 0) {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;
        $role = Role::where('id', $data['id'])->first();
        $this->_id = $role['id'];
        $this->role = $role['menu_access'];
        $this->name = $role['name'];

        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = Role::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfull deleted access right');
        } else {
            session()->flash('error', 'Failed deleted access right');
        }
    }
}
