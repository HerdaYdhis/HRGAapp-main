<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\User;
use App\View\Components\Forms\Select;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ChartAllUserDept extends Component
{

    public $month;
    public $usersStatistics = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $user = DB::table('users')->where('department_id', auth()->user()->department_id)->get();

        $allData = [];

        foreach ($user as $users) {
            $usersStatistics = DB::table('close_periodes')
                ->select(DB::raw('MONTH(created_at) AS month'), 'percentage AS percentage_month')
                ->whereYear('created_at', now()->year)
                ->where('user_id', $users->id)
                ->orderBy('month')
                ->get();

            $month = $usersStatistics->map(function ($data) {
                return date('F', mktime(0, 0, 0, $data->month, 1));
            });

            $usersStatistics = $usersStatistics->pluck('percentage_month')->map(function ($item) {
                return (int) $item;
            });

            $allData[] = [
                'name' => $users->name, // Nama pengguna atau sesuai kebutuhan
                'data' => $usersStatistics->toArray(),
            ];
        }

        // dd($usersStatistics);
        $this->month = $month;
        $this->usersStatistics = $allData;
    }


    public function render()
    {
        return view('livewire.dashboard.chart-all-user-dept');
    }
}
