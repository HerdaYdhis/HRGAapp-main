<?php

namespace App\Http\Livewire\ClosePeriode;

use Carbon\Carbon;
use Exception;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Index extends Component
{

    public function submit()
    {
        DB::beginTransaction();
        try {
            $this->resetErrorBag();

            $users = DB::table('tasks')->select('user_id')->distinct()->get();
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
            $bln = now()->translatedFormat('F');

            if (Carbon::now()->isLastOfMonth()) {
                $errorMessages = [];

                foreach ($users as $user) {
                    $existingClose = DB::table('close_periodes')
                        ->where('user_id', $user->user_id)
                    ->whereMonth('created_at', '=', $endOfMonth)
                        ->first();

                    if ($existingClose) {
                        $name = DB::table('users')->where('id', $user->user_id)->value('name');
                        $errorMessages[] = 'Close Periode bulan ' . $bln . ' untuk user ' . $name . ' sudah tersedia';
                    } else {
                        $done = DB::table('tasks')
                            ->whereIn('type', ['daily', 'weekly', 'monthly'])
                            ->where('user_id', $user->user_id)
                            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                            ->where(function ($query) {
                                $query->where('status', 'done')
                                    ->orWhere('status', 'done with overdue');
                            })
                            ->count();
                        $overdue = DB::table('tasks')
                            ->whereIn('type', ['daily', 'weekly', 'monthly'])
                            ->where('user_id', $user->user_id)
                            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                            ->where('status', 'done with overdue')
                            ->count();

                        $totalTasks = DB::table('tasks')
                            ->whereIn('type', ['daily', 'weekly', 'monthly'])
                            ->where('user_id', $user->user_id)
                            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                            ->where('status', '!=', 'cancelled')
                            ->count();

                        $persen = ($totalTasks > 0) ? ($done / $totalTasks) * 100 : 0;
                        $percentage = number_format($persen, 2, '.', '');



                        DB::table('close_periodes')->insert([
                            'user_id' => $user->user_id,
                            'month' => $endOfMonth,
                            'percentage' => $percentage,
                            'done' => $done,
                            'overdue' => $overdue,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                if (count($errorMessages) > 0) {
                    DB::rollBack();
                    session()->flash('error', implode('<br>', $errorMessages));
                } else {
                    DB::commit();
                    $this->emit('re_render_table');
                    session()->flash('success', 'Successfully closed period for month ' . $bln);
                }
            } else {
                session()->flash('error', 'Sorry, this is not the end of the month date ' . $bln);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Terjadi kesalahan: " . $e->getMessage());
            session()->flash('error', 'Failed to Close Period, Error Message :' . $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.close-periode.index')->layoutData([
            'title' => 'Close Periode'
        ]);
    }
}
