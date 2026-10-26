<?php

namespace App\Http\Livewire\Branch;

use App\Models\Branch;
use Livewire\Component;

class Index extends Component
{
    public $modal = 'branch_modal';

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
            $data = Branch::where('id', $this->_id)->update([
                'name' => $this->name
            ]);
        } else {
            $data = Branch::create([
                'name' => $this->name
            ]);
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved Branch');
        } else {
            session()->flash('error', 'Failed to saved Branch');
        }
    }


    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;

        $branch = Branch::where('id', $data['id'])->first();
        $this->_id = $branch['id'];
        $this->name = $branch['name'];

        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = Branch::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved Branch');
        } else {
            session()->flash('error', 'Failed to saved Branch');
        }
    }

    public function render()
    {
        return view('livewire.branch.index')->layoutData(['title' => 'Branch']);
    }
}
