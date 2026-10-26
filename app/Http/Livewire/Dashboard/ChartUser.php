<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ChartUser extends Component
{

    public $month;
    public $usersStatistics = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $usersStatistics = DB::table('close_periodes')
            ->select(DB::raw('MONTH(created_at) AS month'), 'percentage AS percentage_month')
            ->whereYear('created_at', now()->year)
            ->where('user_id', auth()->user()->id)
            ->orderBy('month')
            ->get();

        $month = $usersStatistics->map(function ($data) {
            return date('F', mktime(0, 0, 0, $data->month, 1));
        });

        $this->month = $month;

        $this->usersStatistics = $usersStatistics->pluck('percentage_month')->map(function ($item) {
            return (int) $item;
        });
    }

    public function render()
    {
        return view('livewire.dashboard.chart-user');
    }
}
