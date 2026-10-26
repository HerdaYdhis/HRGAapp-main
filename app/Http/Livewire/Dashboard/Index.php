<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Task;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Index extends Component
{


    public $modal = 'dashboard_modal';

    public $title, $description, $type, $user_id, $duedate, $status;

    public $is_edit = false;

    public $_id;

    public $userdata;


    protected $listeners = [
        'edit',
        'delete'
    ];

    protected $rules = [
        'title'  => 'required|string',
        'type'  => 'required',
        'user_id'  => 'required',

    ];
    // protected $messages = [
    //     'image.required' => 'The image cannot be empty.',
    // ];


    public function mount()
    {
        $this->userdata();
    }
    public function userdata()
    {
        // $userdata = User::get();
        // return $this->userdata = $userdata;
        $userdata = User::where('department_id', '=', auth()->user()->department_id)->get();
        $this->userdata = $userdata;
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
        $this->is_edit = false;

        $this->title = null;
        $this->description = null;
        $this->type = null;
        $this->user_id = null;
    }


    public function render()
    {
        return view('livewire.dashboard.index',)->layoutData([
            'title' => 'Dashboard',
        ]);
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        if ($this->is_edit) {

            if ($this->duedate) {
                $data = Task::where('id', $this->_id)->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'type' => $this->type,
                    'user_id' => $this->user_id,
                    'status' => $this->status,
                    'duedate' => $this->duedate,
                ]);
            } else {
                $data = Task::where('id', $this->_id)->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'type' => $this->type,
                    'status' => $this->status,
                    'user_id' => $this->user_id,
                ]);
            }
        } else {

            if ($this->duedate) {
                $data = Task::create([
                    'title' => $this->title,
                    'description' => $this->description,
                    'type' => $this->type,
                    'user_id' => $this->user_id,
                    'status' => $this->status,
                    'duedate' => $this->duedate,
                ]);
            } else {
                // dd([$this->type, $this->user_id]);
                $data = Task::create([
                    'title' => $this->title,
                    'description' => $this->description,
                    'type' => $this->type,
                    'user_id' => $this->user_id,
                    'status' => $this->status,
                ]);
            }
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'successfully saved the data');
        } else {
            session()->flash('error', 'Failed saved the data');
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
        $this->status = $data['status'];
        $this->_id = $data['id'];


        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = Task::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'successfully deleted the data');
        } else {
            session()->flash('error', 'Failed deleted the data');
        }
    }


}
