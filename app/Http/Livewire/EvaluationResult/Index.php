<?php

namespace App\Http\Livewire\EvaluationResult;

use App\Models\Evaluation;
use App\Models\EvaluationResult;
use App\Models\Performance;
use App\Models\User;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportResult;
use Illuminate\Support\Facades\DB;
use App\Services\EvaluationCalculatorService;

class Index extends Component
{
    public $modal = 'evaluation_modal';
    /** @var int|float|null */
    public $nilai;
    /** @var int|string|null */
    public $user_id;
    /** @var string|null */
    public $periode;
    /** @var int|string|null */
    public $performance_id;

    public $is_edit = false;

    /** @var int|string|null */
    public $_id;

    /** @var mixed */
    public $othersVisible;
    public $select_user = false;
    /** @var mixed */
    public $userdata;

    protected $listeners = [
        'edit',
        'delete',
        'input_nilai'
    ];

    protected $rules = [
        'nilai' => 'required|numeric|between:0,100',
        'periode' => 'required|date_format:Y-m',
    ];

    // protected $messages = [
    //     'name.required' => 'This image cannot be empty.',
    //     'role.required' => 'This image cannot be empty.',
    // ];

    public function mount()
    {

        $this->userdata();
    }

    public function userdata()
    {
        $userdata = User::get();
        return $this->userdata = $userdata;
    }


    public function render()
    {
        return view('livewire.evaluation-result.index')->layoutData([
            'title' => 'Evaluation'
        ]);
    }

    public function download()
    {
        $periode =  session('sendPeriode') ? \Carbon\Carbon::parse(session('sendPeriode'))->format('F Y') : ' ';
        $deptSuffix = '';
        if (session('filterDepartment')) {
            $dept = \App\Models\Department::find(session('filterDepartment'));
            if ($dept) {
                $deptSuffix = '-' . \Illuminate\Support\Str::slug($dept->name);
            }
        }
        return Excel::download(new ExportResult, 'Rekap-' . $periode . $deptSuffix . '.xlsx');
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
        $this->nilai = null;
    }

    public function back()
    {
        $this->othersVisible = null;
        $this->select_user = false;
    }

    public function user_submit()
    {
        $this->validate([
            'periode' => 'required',
        ]);

        $this->othersVisible = $this->periode;
        $this->select_user = true;
        session()->put('sendPeriode',  $this->periode);

        // Sinkronisasi: Panggil fungsi perhitungan yang sudah ada untuk semua user pada periode ini
        $userIds = DB::table('evaluations')->where('periode', $this->periode)->pluck('user_id')
            ->merge(DB::table('disciplines')->where('periode', $this->periode)->pluck('user_id'))
            ->merge(DB::table('warning_letters')->where('periode', $this->periode)->pluck('user_id'))
            ->unique();

        foreach ($userIds as $userId) {
            EvaluationCalculatorService::recalculate($userId, $this->periode);
        }

        $this->emit('re_render_table');
    }



    public function input_nilai(mixed $data)
    {
        $this->resetErrorBag();
        $this->is_edit = false;
        $this->_id =  $data['id'];
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }


    public function edit(mixed $data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;

        $evaluation = Evaluation::where('id', $data['id'])->first();
        $this->_id = $evaluation['id'];
        $this->user_id = $evaluation['user_id'];
        $this->periode = $evaluation['periode'];
        $this->performance_id = $evaluation['performance_id'];
        $this->nilai = $evaluation['nilai'];

        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete(mixed $data)
    {
        $this->resetErrorBag();

        $id = $data['id'] ?? $data;

        $evaluationDelete = Evaluation::where('id', $id)->first();

        if ($evaluationDelete) {
            $tgl = $evaluationDelete->periode;
            $user_id = $evaluationDelete->user_id;

            $evaluationDelete->delete();
            \App\Services\EvaluationCalculatorService::recalculate($user_id, $tgl);

            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Berhasil menghapus nilai');
        } else {
            session()->flash('error', 'Data tidak ditemukan');
        }
    }
}
