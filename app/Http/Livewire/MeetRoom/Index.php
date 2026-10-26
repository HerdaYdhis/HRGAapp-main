<?php

namespace App\Http\Livewire\Meetroom;

use App\Models\Meetroom;
use Livewire\Component;

class Index extends Component
{

    public $modal = 'meetroom_modal';

    public $is_edit = false;

    public $_id;

    public $meetroom;
    public $name, $location;

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
        return view('livewire.meet-room.index')->layoutData([
            'title' => 'Ruang Meeting'
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
        $this->location = null;
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        if ($this->is_edit) {
            $data = Meetroom::where('id', $this->_id)->update([
                'name' => $this->name,
                'location' => $this->location
            ]);
        } else {
            $data = Meetroom::create([
                'name' => $this->name,
                'location' => $this->location
            ]);
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved Meetroom');
        } else {
            session()->flash('error', 'Failed to saved Meetroom');
        }
    }


    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;
        $this->_id = $data['id'];
        $this->name = $data['name'];
        $this->location = $data['location'];


        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = Meetroom::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved Meet Room');
        } else {
            session()->flash('error', 'Failed to saved Meet Room');
        }
    }
}
