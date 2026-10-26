<?php

namespace App\Http\Livewire\DetailTask;

use App\Models\DetailTask;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Carbon\Carbon;

class Index extends Component
{
    public $modal = 'detail_task_modal';

    public $title, $description, $status, $percentage_complete, $parameter;

    public $is_edit = false;

    public $_id;

    public $detail_task;

    protected $listeners = [
        'edit',
        'delete'
    ];

    protected $rules = [
        'title' => 'required|string'
    ];

    // protected $messages = [
    //     'name.required' => 'This image cannot be empty.',
    //     'role.required' => 'This image cannot be empty.',
    // ];

    function back()
    {
        return Redirect::to('/task#list_project');
    }


    public function mount($id)
    {
        $task = Task::where('id', $id)->first();
        if (!$task) {
            return Redirect::to('/task');
        }
        if ($task->user_id != auth()->user()->id) {
            return Redirect::to('/task');
        }

        $this->parameter = $id;
        session()->put('sendParameterTask',  $this->parameter);
    }

    public function render()
    {
        return view('livewire.detail_task.index')->layoutData([
            'title' => 'Detail Task'
        ]);
    }
    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset_form();
        $this->is_edit = false;
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function closeModal()
    {
        $this->resetErrorBag();
        $this->reset_form();
        $this->is_edit = false;
        $this->dispatchBrowserEvent('modal-hide', ['modal' => $this->modal]);
    }

    public function reset_form()
    {
        $this->title = null;
        $this->description = null;
        $this->status = null;
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        if ($this->is_edit) {
            if ($this->status == 'done') {
                $this->percentage_complete = 100;
            } else {
                $this->percentage_complete = 0;
            }
            $data = DetailTask::where('id', $this->_id)->update([
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'percentage_complete' => $this->percentage_complete,
            ]);

            $count_task = DetailTask::where('task_id', $this->parameter)->where('status', '<>', 'cancelled')->count();
            $check_status = DetailTask::where('task_id', $this->parameter)->where('status', 'done')->count();

            if ($check_status == 0 && $this->status == 'cancelled') {
                $data = DB::table('tasks')->where('id', $this->parameter)->update([
                    'status' => 'open',
                    'updated_at' => now()
                ]);

                $this->emit('re_render_table');
                $this->closeModal();
                return false;
            }

            if ($count_task == $check_status) {
                $task = Task::where('id', $this->parameter)->first();
                $createddate = Carbon::parse($task->duedate)->format('Y-m-d');
                $now = Carbon::parse(now())->format('Y-m-d');
                $cekduedate =   $now > $createddate;

                if ($cekduedate && $this->status === 'done') {
                    $data = DB::table('tasks')->where('id', $this->parameter)->update([
                        'status' => 'done with overdue',
                        'updated_at' => now()
                    ]);
                } else {
                    $data = DB::table('tasks')->where('id', $this->parameter)->update([
                        'status' => 'done',
                        'updated_at' => now()
                    ]);
                }
            } else {
                $data = DB::table('tasks')->where('id', $this->parameter)->update([
                    'status' => 'open',
                    'updated_at' => now()
                ]);
            }
        } else {
            $data = DetailTask::create([
                'task_id' => $this->parameter,
                'title' => $this->title,
                'description' => $this->description,
            ]);

            $count_task = DetailTask::where('task_id', $this->parameter)->where('status', '<>', 'cancelled')->count();
            $check_status = DetailTask::where('task_id', $this->parameter)->where('status', 'done')->count();

            if ($count_task == $check_status) {
                $task = Task::where('id', $this->parameter)->first();
                $createddate = Carbon::parse($task->duedate)->format('Y-m-d');
                $now = Carbon::parse(now())->format('Y-m-d');
                $cekduedate =   $now > $createddate;
                if ($cekduedate && $this->status === 'done') {
                    $data = DB::table('tasks')->where('id', $this->parameter)->update([
                        'status' => 'done with overdue',
                        'updated_at' => now()
                    ]);
                } else {
                    $data = DB::table('tasks')->where('id', $this->parameter)->update([
                        'status' => 'done',
                        'updated_at' => now()
                    ]);
                }
            } else {
                $data = DB::table('tasks')->where('id', $this->parameter)->update([
                    'status' => 'open',
                    'updated_at' => now()
                ]);
            }
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved task');
        } else {
            session()->flash('error', 'Failed to saved task');
        }
    }


    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;
        $detail_task = DetailTask::where('id', $data['id'])->first();
        $this->_id = $detail_task['id'];
        $this->title = $detail_task['title'];
        $this->description = $detail_task['description'];
        $this->status = $detail_task['status'];

        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $id = $data['id'];
        $this->resetErrorBag();


        $detail_task = DetailTask::where('id', $id)->first();
        $task = Task::where('id', $detail_task->task_id)->first();
        $data = DetailTask::where('id', $id)->delete();

        $count_task = DetailTask::where('task_id', $task->id)->where('status', '<>', 'cancelled')->count();
        $check_status = DetailTask::where('task_id', $task->id)->where('status', 'done')->count();

        if ($count_task == $check_status) {
            $createddate = Carbon::parse($task->duedate)->format('Y-m-d');
            $now = Carbon::parse(now())->format('Y-m-d');
            $cekduedate =   $now > $createddate;

            if ($cekduedate && $this->status === 'done') {
                $data = DB::table('tasks')->where('id', $task->id)->update([
                    'status' => 'done with overdue',
                    'updated_at' => now()
                ]);
            } else {
                $data = DB::table('tasks')->where('id', $task->id)->update([
                    'status' => 'done',
                    'updated_at' => now()
                ]);
            }
        } else {
            $data = DB::table('tasks')->where('id', $task->id)->update([
                'status' => 'open',
                'updated_at' => now()
            ]);
        }



        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Success deleted task');
        } else {
            session()->flash('error', 'Failed to deleted task');
        }
    }
}
