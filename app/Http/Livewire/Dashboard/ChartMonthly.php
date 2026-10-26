<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ChartMonthly extends Component
{
    public $usersStatistics = [];
    public $month;
    public $users = [];
    public $selectedUserId;
    public $open;
    public $sum;
    public $overdue;
    public $persen;
    public $emote;
    public $motivasi;
    public $doneproject;
    public $openproject;
    public $overdueproject;



    protected $listeners = [
        'loadData',
    ];

    public function mount()
    {

        $user = User::where('department_id', auth()->user()->department_id)->where('parent_id', '!=', null)->get();
        $this->users = $user;


        $this->loadData();

    }

    public function render()
    {
        return view('livewire.dashboard.chart-monthly');
    }

    public function loadData()
    {
        if ($this->selectedUserId === '') {
            $this->emit('re_render_table');
            $this->selectedUserId = null;
        }


        if ($this->selectedUserId) {

            $usersStatistics = DB::table('close_periodes')
                ->select(DB::raw('MONTH(created_at) AS month'), 'percentage AS percentage_month')
                ->whereYear('created_at', now()->year)
                ->where('user_id', $this->selectedUserId)
                ->orderBy('month')
                ->get();

            if ($usersStatistics->isNotEmpty()) {

                $month = $usersStatistics->map(function ($data) {
                    return date('F', mktime(0, 0, 0, $data->month, 1));
                });

                $this->month = $month;
                $this->usersStatistics = $usersStatistics->pluck('percentage_month');

                $this->emit('refreshChartData', [
                    'categories' => $this->month,
                    'seriesData' => $this->usersStatistics,
                    'seriesName' => 'Percentage',
                ]);

                $this->sumValue();
                $this->emit('re_render_table');
            } else {
            }
        } else {

            $this->resetData();
        }

        // $this->emit('setSelectedUserId', $this->selectedUserId);
        session()->put('selectedUserId', $this->selectedUserId);


        // dd($this->selectedUserId);

        $this->sumValue();
    }

    public function resetData()
    {
        $this->month = collect();
        $this->usersStatistics = collect();
    }


    public function sumValue()
    {

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $done = DB::table('tasks')
            ->whereIn('type', ['daily', 'weekly', 'monthly'])
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where(function ($query) {
                $query->where('status', 'done')
                ->orWhere('status', 'done with overdue');
            })->where('user_id', $this->selectedUserId)
            ->count();
        $open = DB::table('tasks')
        ->whereIn('type', ['daily', 'weekly', 'monthly'])
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->where(function ($query) {
            $query->where('status', 'open');
        })->where('user_id', $this->selectedUserId)
        ->count();

        $overdue = DB::table('tasks')
            ->whereIn('type', ['daily', 'weekly', 'monthly'])
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where(function ($query) {
                $query->orWhere('status', 'done with overdue');
            })->where('user_id', $this->selectedUserId)
            ->count();

        $totalTasks = DB::table('tasks')
            ->whereIn('type', ['daily', 'weekly', 'monthly'])
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])->where('user_id', $this->selectedUserId)
            ->where('status', '!=', 'cancelled')
            ->count();

        $doneproject = DB::table('tasks')
            ->where('type', 'project')
            ->whereYear('created_at', now())
            ->where(function ($query) {
                $query->orWhere('status', 'done with overdue')
                ->orWhere('status', 'done');
            })
            ->where('user_id', $this->selectedUserId)
            ->count();

        $openproject = DB::table('tasks')
            ->where('type', 'project')
            ->whereYear('created_at', now())
            ->where(function ($query) {
                $query->orWhere('status', 'open');
            })
            ->where('user_id', $this->selectedUserId)
            ->count();

        $overdueproject = DB::table('tasks')
        ->where('type', 'project')
        ->whereYear('created_at', now())
            ->where(function ($query) {
                $query->orWhere('status', 'done with overdue');
            })
        ->where('user_id', $this->selectedUserId)
        ->count();

        // dd($doneproject);
        $persen = ($totalTasks > 0) ? ($done / $totalTasks) * 100 : 0;

        $persen = number_format($persen, 2, '.', '');


        $this->open = $open;
        $this->sum = $done;
        $this->persen = $persen;
        $this->overdue = $overdue;

        $this->doneproject = $doneproject;
        $this->openproject = $openproject;
        $this->overdueproject = $overdueproject;


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

        $this->emit('refreshNilai', [
            'open' => $this->open,
            'sum' => $this->sum,
            'percent' => $this->persen,
            'overdue' => $this->overdue,
            'emote' => $this->emote,
            'motivasi' => $this->motivasi,
            'doneproject' => $this->doneproject,
            'openproject' => $this->openproject,
            'overdueproject' => $this->overdueproject,



        ]);


    }
}
