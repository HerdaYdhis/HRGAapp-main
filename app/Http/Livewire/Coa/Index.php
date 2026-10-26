<?php

namespace App\Http\Livewire\Coa;

use App\Models\Coa;
use Livewire\Component;

class Index extends Component
{
    public $modal = 'coa_modal';

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
        return view('livewire.coa.index')->layoutData([
            'title' => 'COA'
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
            $data = Coa::where('id', $this->_id)->update([
                'name' => $this->name
            ]);
        } else {
            $data = Coa::create([
                'name' => $this->name
            ]);
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved COA');
        } else {
            session()->flash('error', 'Failed to saved COA');
        }
    }


    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;

        $coa = Coa::where('id', $data['id'])->first();
        $this->_id = $coa['id'];
        $this->name = $coa['name'];

        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = Coa::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved COA');
        } else {
            session()->flash('error', 'Failed to saved COA');
        }
    }
}
