<?php

namespace App\Http\Livewire\CostCenter;

use App\Models\CostCenter;
use Livewire\Component;

class Index extends Component
{
    public $modal = 'cost-center_modal';

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



    public function render()
    {
        return view('livewire.cost-center.index')->layoutData([
            'title' => 'Cost Center'
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
            $data = CostCenter::where('id', $this->_id)->update([
                'name' => $this->name
            ]);
        } else {
            $data = CostCenter::create([
                'name' => $this->name
            ]);
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved Cost Center');
        } else {
            session()->flash('error', 'Failed to saved Cost Center');
        }
    }


    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;

        $costcenter = CostCenter::where('id', $data['id'])->first();
        $this->_id = $costcenter['id'];
        $this->name = $costcenter['name'];

        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = CostCenter::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved Cost Center');
        } else {
            session()->flash('error', 'Failed to saved Cost Center');
        }
    }
}
