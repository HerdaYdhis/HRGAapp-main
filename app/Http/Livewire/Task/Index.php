<?php

namespace App\Http\Livewire\Task;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\Task;
use Livewire\Component;
use App\Models\MasterTask;
use Livewire\WithFileUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class Index extends Component
{
    use WithFileUploads;

    public $modal = 'task_modal';

    public $image, $status, $type, $duedate, $progress, $description, $title;

    public $is_edit = false;

    public $_id;

    public $mastertask;

    public $sum, $persen, $overdue, $emote, $motivasi, $totalproject, $open, $openproject, $doneproject, $overdueproject;

    public $parameter;
    public $create_project = false;

    protected $listeners = [
        'toDetailTask',
        'edit',
        'delete',
        'cancelled',
        'refreshNilai' => '$sumValue',
    ];

    protected $rules = [
        'status'  => 'required',


    ];

    public function mount()
    {
        $this->emit('re_render_table');

        $this->mastertask();
        $this->sumValue();
    }

    public function mastertask()
    {
        $mastertask = MasterTask::get();
        return $this->mastertask = $mastertask;
    }

    public function openModal()
    {
        $this->create_project = false;
        $this->resetErrorBag();
        $this->reset_form();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function openModalProject()
    {
        $this->create_project = true;
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
        $this->image = null;
        $this->title = null;
        $this->description = null;
        $this->progress = null;
        $this->duedate = null;
        $this->status = null;
        $this->is_edit = false;
    }

    public function render()
    {
        return view('livewire.task.index')->layoutData([
            'title' => 'Task'
        ]);
    }

    public function project_insert()
    {
        if ($this->progress == null) {
            $progress = 0;
        } else {
            $progress = $this->progress;
        }

        $data = Task::create([
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => auth()->user()->id,
            'type' => 'project',
            'progress' =>  $progress,
            'duedate' => $this->duedate,
        ]);
        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            $this->reset_form();
            session()->flash('success', 'Success saved Project Task');
        } else {
            session()->flash('error', 'Failed saved Project Task');
        }
    }

    public function submit()
    {
        DB::beginTransaction();

        try {
            $this->resetErrorBag();
            if ($this->is_edit) {
                if ($this->image && $this->image instanceof UploadedFile) {
                    $old_img = DB::table('tasks')
                        ->where('id', $this->_id)
                        ->value('image');
                    if ($old_img) {
                        Storage::disk('public')->delete($old_img);
                    }
                    $newImagePath = $this->image->store(
                        'tasks',
                        'public'
                    );
                    DB::table('tasks')
                        ->where('id', $this->_id)
                        ->update(['image' => $newImagePath]);
                }
                $cek = DB::table('tasks')->where('id', $this->_id)->get();

                foreach ($cek as $date) {
                    $daydate = Carbon::now()->format('Y-m-d');
                    $daynow = Carbon::parse($date->created_at)->format('Y-m-d');
                    $cekdate = $daydate === $daynow;

                    $weekdate = Carbon::parse($date->created_at)->format('Y-m-d');
                    $weeknow = Carbon::now()->endOfWeek()->format('Y-m-d');
                    $cekweek =  $weekdate <= $weeknow;

                    $createddate = Carbon::parse($date->duedate)->format('Y-m-d');
                    $now = Carbon::parse(now())->format('Y-m-d');
                    $cekduedate =   $now > $createddate;

                    if ($date->type === 'daily' && !$cekdate && $this->status === 'done') {
                        $data = DB::table('tasks')->where('id', $this->_id)->update([
                            'status' => 'done with overdue',
                            'updated_at' => now()
                        ]);
                    } elseif ($date->type === 'weekly' && !$cekweek && $this->status === 'done') {
                        $data = DB::table('tasks')->where('id', $this->_id)->update([
                            'status' => 'done with overdue',
                            'updated_at' => now()
                        ]);
                    } elseif ($date->type === 'monthly' && $cekduedate && $this->status === 'done') {
                        $data = DB::table('tasks')->where('id', $this->_id)->update([
                            'status' => 'done with overdue',
                            'updated_at' => now()
                        ]);
                    } elseif ($date->type === 'project' && $cekduedate && $this->status === 'done') {
                        $data = DB::table('tasks')->where('id', $this->_id)->update([
                            'status' => 'done with overdue',
                            'updated_at' => now()
                        ]);
                    } else {
                        $data = DB::table('tasks')->where('id', $this->_id)->update([
                            'status' => $this->status,
                            'updated_at' => now()
                        ]);
                    }
                }
            } else {
                $mt = $this->mastertask();
                $today = Carbon::now();
                foreach ($mt as $mastertask) {

                    if ($mastertask->user_id !== auth()->user()->id) {
                        continue;
                    }

                    if ($mastertask->type === 'daily') {
                        $existingTask = DB::table('tasks')
                            ->where('title', $mastertask->title)
                            ->whereDate('created_at', '=', $today->toDateString())
                            ->lockForUpdate()
                            ->exists();
                    } elseif ($mastertask->type === 'weekly') {
                        $existingTask = DB::table('tasks')
                            ->where('title', $mastertask->title)
                            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                            ->lockForUpdate()
                            ->exists();
                    } elseif ($mastertask->type === 'monthly') {
                        $existingTask = DB::table('tasks')
                            ->where('title', $mastertask->title)
                            ->whereMonth('created_at', '=', $today->month)
                            ->lockForUpdate()
                            ->exists();
                    }
                    //  elseif ($mastertask->type === 'project') {
                    //     $existingTask = DB::table('tasks')
                    //         ->where('title', $mastertask->title)
                    //         ->whereDate('created_at', '<=', $mastertask->duedate)
                    //         ->lockForUpdate()
                    //         ->exists();
                    // }

                    // dd($existingTask);
                    if (!$existingTask) {

                        //cek existingTask
                        if ($mastertask->duedate) {

                            $data = DB::table('tasks')->insert([
                                'title' => $mastertask->title,
                                'description' => $mastertask->description,
                                'user_id' => $mastertask->user_id,
                                'type' => $mastertask->type,
                                'duedate' => $mastertask->duedate,
                                'progress' => false,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } else {
                            $data = DB::table('tasks')->insert([
                                'title' => $mastertask->title,
                                'description' => $mastertask->description,
                                'user_id' => $mastertask->user_id,
                                'type' => $mastertask->type,
                                'progress' => false,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        // dd("hehe");
                        // session()->flash('error', 'Data sudah ada untuk ' . $mastertask->title . ' pada ' . $today->toDateString());
                    }

                    if ($mastertask->type == 'monthly') {
                        $mastertask->update(['duedate' => Carbon::parse($mastertask->duedate)->addMonth(1)]);
                    }
                }
            }

            DB::commit();
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Succesfully Bulk');
            $this->sumValue();
        } catch (Exception $e) {
            DB::rollBack();
            // Tangani pengecualian di sini
            Log::error("Error Message: " . $e->getMessage());
            session()->flash('error', 'Failed a Bulk Message: ' . $e->getMessage());
            // session()->flash('error', 'Gagal melakukan Bulk');

        }
    }

    public function edit($data)
    {
        $this->resetErrorBag();

        $this->is_edit = true;
        $this->create_project = false;
        $this->status = $data['status'];
        $this->image = $data['image'];

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
            session()->flash('success', 'Success deleted task');
        } else {
            session()->flash('error', 'Failed deleted Task');
        }
    }

    public function cancelled($data)
    {
        $data = DB::table('tasks')->where('id', $data['id'])->update([
            'status' => 'cancelled',
            'updated_at' => now()
        ]);

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Success cencelled task');
        } else {
            session()->flash('error', 'Failed cencelled Task');
        }
    }

    public function sumValue()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $open = DB::table('tasks')
            ->whereIn('type', ['daily', 'weekly', 'monthly'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where(function ($query) {
                $query->where('status', 'open');
            })->where('user_id', auth()->user()->id)
            ->count();

        $done = DB::table('tasks')
            ->whereIn('type', ['daily', 'weekly', 'monthly'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where(function ($query) {
                $query->where('status', 'done')
                    ->orWhere('status', 'done with overdue');
            })->where('user_id', auth()->user()->id)
            ->count();

        $overdue = DB::table('tasks')
            ->whereIn('type', ['daily', 'weekly', 'monthly'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where(function ($query) {
                $query->orWhere('status', 'done with overdue');
            })->where('user_id', auth()->user()->id)
            ->count();

        $totalTasks = DB::table('tasks')
            ->whereIn('type', ['daily', 'weekly', 'monthly'])
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])->where('user_id', auth()->user()->id)
            ->count();

        $totalproject = DB::table('tasks')
            ->where('type', 'project')
            ->where('status', '!=', 'cancelled')
            ->whereYear('created_at', now())
            ->where('user_id', auth()->user()->id)
            ->count();

        $openproject = DB::table('tasks')
            ->where('type', 'project')
            ->where('status', 'open')
            ->whereYear('created_at', now())
            ->where('user_id', auth()->user()->id)
            ->count();

        $doneproject = DB::table('tasks')
            ->where('type', 'project')
            ->where('status', 'done')
            ->whereYear('created_at', now())
            ->where('user_id', auth()->user()->id)
            ->count();

        $overdueproject = DB::table('tasks')
            ->where('type', 'project')
            ->where('status', 'done with overdue')
            ->whereYear('created_at', now())
            ->where('user_id', auth()->user()->id)
            ->count();

        // dd($totalproject);

        $persen = ($totalTasks > 0) ? ($done / $totalTasks) * 100 : 0;

        $persen = number_format($persen, 2, '.', '');


        $this->open = $open;
        $this->sum = $done;
        $this->persen = $persen;
        $this->overdue = $overdue;
        $this->totalproject = $totalproject;
        $this->openproject = $openproject;
        $this->doneproject = $doneproject;
        $this->overdueproject = $overdueproject;

        // dd([$this->open, $this->sum, $this->overdue, $this->totalproject, $this->openproject, $this->doneproject, $this->overdueproject]);

        if ($persen == 100) {
            $this->emote = '🥰';
            $this->motivasi = 'Very Well, Good Job';
        } elseif ($persen >= 95) {
            $this->emote = '🤩';
            $this->motivasi = 'Good, Almost there!';
        } elseif ($persen >= 90) {
            $this->emote = '😊';
            $this->motivasi = 'Enough, Great job!';
        } elseif ($persen >= 80) {
            $this->emote = '😄';
            $this->motivasi = 'Not enough, Keep up the good work!';
        } elseif ($persen >= 70) {
            $this->emote = '😐';
            $this->motivasi = 'Stay motivated!';
        } else {
            $this->emote = '😭';
            $this->motivasi = 'Don\'t give up!';
        }
    }

    public function toDetailTask($parameter)
    {
        return Redirect::to('/detail-task/' . $parameter);
    }

    public function getChartData()
    {
        // Logika untuk mengambil data terbaru dari database atau sumber data lainnya
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $done = DB::table('tasks')
            ->whereIn('type', ['daily', 'weekly', 'monthly'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where(function ($query) {
                $query->where('status', 'done')
                    ->orWhere('status', 'done with overdue');
            })->where('user_id', auth()->user()->id)
            ->count();

        // $overdue = DB::table('tasks')
        //     ->whereIn('type', ['daily', 'weekly', 'monthly'])
        //     ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
        //     ->where(function ($query) {
        //         $query->orWhere('status', 'done with overdue');
        //     })->where('user_id', auth()->user()->id)
        //     ->count();

        $totalTasks = DB::table('tasks')
            ->whereIn('type', ['daily', 'weekly', 'monthly'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])->where('user_id', auth()->user()->id)
            ->where('status', '!=', 'cancelled')
            ->count();

        // $totalproject = DB::table('tasks')
        //     ->where('type', 'project')
        //     ->whereYear('created_at', now())
        //     ->where('user_id', auth()->user()->id)
        //     ->count();

        // // dd($totalproject);

        $persen = ($totalTasks > 0) ? ($done / $totalTasks) * 100 : 0;
        $persen = number_format($persen, 2);
        return response()->json(['persen' => $persen]);
    }
}
