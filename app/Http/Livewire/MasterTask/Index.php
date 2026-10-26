<?php

namespace App\Http\Livewire\MasterTask;

use App\Models\MasterTask;
use App\Models\Task;
use App\Models\User;
use Livewire\Component;

class Index extends Component
{

    public $modal = 'mastertask_modal';

    public $title, $description, $type, $user_id, $duedate;

    public $is_edit = false;

    public $_id;

    protected $listeners = [
        'edit',
        'delete'
    ];

    protected $rules = [
        'title'  => 'required|string',
        'type'  => 'required',
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
        $this->is_edit = false;

        $this->title = null;
        $this->description = null;
        $this->type = null;
        $this->duedate = null;
        $this->user_id = null;
    }

    public function render()
    {
        return view('livewire.master-task.index')->layoutData([
            'title' => 'Master Task'
        ]);
    }

    public function submit()
    {
        // dd($this->all());
        $this->resetErrorBag();
        $this->validate();

        if ($this->is_edit) {

            if ($this->duedate) {
                $data = MasterTask::where('id', $this->_id)->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'type' => $this->type,
                    'user_id' => auth()->user()->id,
                    'duedate' => $this->duedate,
                ]);
            } else {
                $data = MasterTask::where('id', $this->_id)->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'type' => $this->type,
                    'user_id' => auth()->user()->id,
                ]);
            }
        } else {

            if ($this->duedate) {

                $data = MasterTask::create([
                    'title' => $this->title,
                    'description' => $this->description,
                    'type' => $this->type,
                    'user_id' => auth()->user()->id,
                    'duedate' => $this->duedate,
                ]);
            } else {

                $data = MasterTask::create([
                    'title' => $this->title,
                    'description' => $this->description,
                    'type' => $this->type,
                    'user_id' => auth()->user()->id,
                ]);
            }
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Success saved Master Task');
        } else {
            session()->flash('error', 'Failed saved Master Task');
        }
    }

    public function edit($data)
    {

        $this->resetErrorBag();

        $this->is_edit = true;

        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->type = $data['type'];
        $this->user_id = $data['user_id'];
        $this->duedate = $data['duedate'];
        $this->_id = $data['id'];


        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = MasterTask::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Success deleted Mastertask');
        } else {
            session()->flash('error', 'Failed deleted Mastertask');
        }
    }
}
