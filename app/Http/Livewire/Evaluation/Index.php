<?php

namespace App\Http\Livewire\Evaluation;

use App\Models\Evaluation;
use App\Models\Performance;
use App\Models\EvaluationResult;
use App\Models\User;
use Livewire\Component;

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
    /** @var string|null */
    public $indikator;
    /** @var int|float|null */
    public $bobot;
    public $is_edit = false;

    /** @var int|string|null */
    public $_id;

    /** @var mixed */
    public $othersVisible;
    public $select_user = false;
    // public $performance;
    /** @var mixed */
    public $userdata;
    /** @var string|null */
    public $username;

    protected $listeners = [
        'edit',
        'delete',
        'input_nilai'
    ];

    protected $rules = [
        'user_id' => 'required',
        'periode' => 'required',
        'nilai' => 'required|numeric|between:0,100',
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
        if (auth()->user()->is_superadmin) {
            $userdata = User::get();
        } else {
            $userdata = User::where('parent_id', auth()->user()->id)->get();
        }
        return $this->userdata = $userdata;
    }


    public function render()
    {
        $performances = Performance::where('user_id', $this->othersVisible)->with(['evaluation' => function ($query) {
            $query->where('periode', $this->periode);
        }])->get();

        return view('livewire.evaluation.index', ['performances' => $performances])->layoutData([
            'title' => 'Evaluation'
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
        $this->nilai = null;
    }

    public function back()
    {
        // $this->location->refresh();
        $this->othersVisible = null;
        $this->select_user = false;
        $this->emit('select2-reinitialize');
    }

    public function user_submit()
    {
        $this->validate([
            'periode' => 'required',
        ]);
        if ($this->user_id == null) {
            $name = User::where('id', auth()->user()->id)->first();
            $this->othersVisible = auth()->user()->id;
        } else {
            $name = User::where('id', $this->user_id)->first();
            $this->othersVisible = $this->user_id;
        }

        $this->username = $name->name;
        $this->select_user = true;
        session()->put('sendUserId',  $this->othersVisible);
        session()->put('sendPeriode',  $this->periode);
    }

    public function submit()
    {
        if (empty($this->user_id) && !empty($this->othersVisible)) {
            $this->user_id = $this->othersVisible;
        }

        $this->resetErrorBag();
        $this->validate();

        $tgl = $this->periode;
        if ($this->is_edit) {
            $bobotPerformance_edit = Performance::where('id', $this->performance_id)->first();
            $bobot = $bobotPerformance_edit ? $bobotPerformance_edit->bobot : 0;
            $nilai_akhir_evaluation_edit = round(($this->nilai / 100) * $bobot, 2);

            Evaluation::where('id', $this->_id)->update([
                'nilai' => $this->nilai,
                'nilai_akhir' => $nilai_akhir_evaluation_edit,
            ]);

            \App\Services\EvaluationCalculatorService::recalculate($this->user_id, $tgl);

            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Berhasil menyimpan nilai');
        } else {
            $check_bobot = Performance::where('user_id', $this->user_id)->sum('bobot');
            if ($check_bobot < 100) {
                $this->closeModal();
                session()->flash('error', 'Total bobot kinerja kurang dari 100, silahkan isi terlebih dahulu indikator dan bobot');
            } else {
                $check = Evaluation::where('performance_id', $this->_id)
                    ->where('user_id', $this->user_id)
                    ->where('periode', $tgl)
                    ->first();

                if ($check) {
                    session()->flash('error', 'Indikator Kinerja sudah terisi nilai pada periode ini');
                    $this->closeModal();
                } else {
                    $bobotPerformance = Performance::where('id', $this->_id)->first();
                    $bobot = $bobotPerformance ? $bobotPerformance->bobot : 0;
                    $nilai_akhir_evaluation = round(($this->nilai / 100) * $bobot, 2);

                    Evaluation::create([
                        'performance_id' => $this->_id,
                        'user_id' => $this->user_id,
                        'nilai' => $this->nilai,
                        'periode' => $this->periode,
                        'nilai_akhir' => $nilai_akhir_evaluation,
                    ]);

                    $evaluationUserCount = Evaluation::where('user_id', $this->user_id)->where('periode', $tgl)->count();
                    $performanceUserCount = Performance::where('user_id', $this->user_id)->count();

                    if ($evaluationUserCount == $performanceUserCount) {
                        \App\Services\EvaluationCalculatorService::recalculate($this->user_id, $tgl);
                    }

                    $this->emit('re_render_table');
                    $this->closeModal();
                    session()->flash('success', 'Berhasil menyimpan nilai');
                }
            }
        }
    }


    public function input_nilai(mixed $data)
    {
        $this->resetErrorBag();
        $this->is_edit = false;
        $this->_id = $data;
        $this->user_id = $this->othersVisible;
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }


    public function edit(mixed $data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;

        $evaluation = Evaluation::where('id', $data)->first();
        if ($evaluation) {
            $this->_id = $evaluation->id;
            $this->user_id = $evaluation->user_id;
            $this->periode = $evaluation->periode;
            $this->performance_id = $evaluation->performance_id;
            $this->nilai = $evaluation->nilai;

            $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
        }
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
            session()->flash('success', 'Berhasil menghapus nilai evaluasi');
        } else {
            session()->flash('error', 'Gagal menghapus: Data tidak ditemukan');
        }
    }
}
