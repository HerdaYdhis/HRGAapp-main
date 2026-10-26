<?php

namespace App\Http\Livewire\Discipline;

use Livewire\WithFileUploads;
use App\Models\Discipline;
use App\Models\EvaluationResult;
use App\Models\User;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportDiscipline;
use App\Exports\ExportDisciplineTemplate;
use App\Imports\ImportDiscipline;

class Index extends Component
{
    use WithFileUploads;
    public $modal = 'Discipline_modal';
    public $is_upload_excel = false;
    /** @var int|float|null */
    public $alfa;
    /** @var int|float|null */
    public $sakit;
    /** @var int|float|null */
    public $izin;
    /** @var int|float|null */
    public $nilai;
    /** @var string|null */
    public $periode;
    /** @var int|string|null */
    public $user_id;
    /** @var int|float|string|null */
    public $terlambat;

    public $is_edit = false;

    /** @var int|string|null */
    public $_id;

    /** @var mixed */
    public $user_select;
    /** @var mixed */
    public $periode_select;
    public $list_user = false;
    /** @var mixed */
    public $discipline;
    /** @var mixed */
    public $userdata;
    /** @var mixed */
    public $excelFile;

    protected $listeners = [
        'edit',
        'delete',
        'input_nilai'
    ];

    protected $rules = [
        'user_id' => 'required',
        // 'alfa' => 'required|numeric|between:0,100',
        // 'izin' => 'required|numeric|between:0,100',
        // 'sakit' => 'required|numeric|between:0,100',
        // 'terlambat' => 'required|numeric|between:0,100',
        'periode' => 'required|date_format:Y-m',
    ];



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
        return view('livewire.discipline.index')->layoutData([
            'title' => 'Penilaian Kedisiplinan'
        ]);
    }
    public function openModal()
    {
        $this->is_upload_excel = false;
        $this->resetErrorBag();
        $this->reset_form();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function openModalExcel()
    {
        $this->is_upload_excel = true;
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
        $this->user_select = null;
        $this->list_user = false;
    }

    public function user_submit()
    {
        $this->user_select = $this->user_id;
        $this->list_user = true;
    }


    public function download()
    {
        return Excel::download(new ExportDiscipline, 'Kedisiplinan.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new ExportDisciplineTemplate, 'template_kedisiplinan.xlsx');
    }

    public function import()
    {
        try {
            $this->validate([
                'excelFile' => 'required|mimes:xls,xlsx',
            ]);

            $import = new ImportDiscipline;
            Excel::import($import, $this->excelFile);

            $count = $import->getImportedCount();
            session()->flash('success', "Berhasil mengimpor {$count} data kedisiplinan.");
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan impor: ' . $e->getMessage());
        }

        $this->closeModal();
        $this->emit('re_render_table');
    }


    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        $tgl = $this->periode;
        $scores = \App\Services\EvaluationCalculatorService::calculateDisciplineScore(
            $this->alfa,
            $this->izin,
            $this->sakit,
            ($this->terlambat == 10 ? 1 : 0),
            ($this->terlambat == 20 ? 1 : 0),
            ($this->terlambat == 30 ? 1 : 0)
        );

        $getUser = User::where('id', $this->user_id)->with('UserLevel')->first();
        $persentaseKedisiplinan = (float) ($getUser->userLevel->persentase_kedisiplinan ?? 0);
        $nilai_akhir = round(($persentaseKedisiplinan / 100) * $scores['nilai_kedisiplinan'], 2);

        if ($this->is_edit) {
            Discipline::where('id', $this->_id)->update([
                'user_id' => $this->user_id,
                'alfa' => $scores['alfa'],
                'sakit' => $scores['sakit'],
                'izin' => $scores['izin'],
                'terlambat1' => $scores['terlambat1'],
                'terlambat2' => $scores['terlambat2'],
                'terlambat3' => $scores['terlambat3'],
                'nilai_kedisiplinan' => $scores['nilai_kedisiplinan'],
                'nilai' => $nilai_akhir,
                'periode' => $this->periode
            ]);
        } else {
            $check = Discipline::where('user_id', $this->user_id)->where('periode', $tgl)->first();
            if ($check) {
                session()->flash('error', 'Nilai kedisiplinan sudah terisi nilai pada periode ini');
                $this->closeModal();
                return;
            }

            Discipline::create([
                'user_id' => $this->user_id,
                'alfa' => $scores['alfa'],
                'sakit' => $scores['sakit'],
                'izin' => $scores['izin'],
                'terlambat1' => $scores['terlambat1'],
                'terlambat2' => $scores['terlambat2'],
                'terlambat3' => $scores['terlambat3'],
                'nilai_kedisiplinan' => $scores['nilai_kedisiplinan'],
                'nilai' => $nilai_akhir,
                'periode' => $this->periode
            ]);
        }

        \App\Services\EvaluationCalculatorService::recalculate($this->user_id, $tgl);

        $this->user_id = "";
        $this->periode = "";
        $this->alfa = "";
        $this->sakit = "";
        $this->izin = "";
        $this->terlambat = "";

        $this->emit('re_render_table');
        $this->closeModal();
        session()->flash('success', 'Berhasil menyimpan nilai kedisiplinan');
    }

    public function input_nilai(array $data)
    {
        $this->resetErrorBag();
        $this->is_edit = false;
        $this->_id =  $data['id'];
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }


    public function edit(array $data)
    {
        $this->resetErrorBag();
        $this->is_upload_excel = false;
        $this->is_edit = true;

        $discipline = Discipline::where('id', $data['id'])->first();
        $this->_id = $discipline['id'];
        $this->user_id = $discipline['user_id'];
        $this->periode = $discipline['periode'];
        $this->alfa = $discipline['alfa'];
        $this->sakit = $discipline['sakit'];
        $this->izin = $discipline['izin'];
        if ($discipline['terlambat1'] == 1) {
            $this->terlambat = 10;
        } elseif ($discipline['terlambat2'] == 1) {
            $this->terlambat = 20;
        } elseif ($discipline['terlambat3'] == 1) {
            $this->terlambat = 30;
        } else {
            $this->terlambat = 0;
        }

        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete(array $data)
    {
        $this->resetErrorBag();
        $id = $data['id'];
        $evaluationDelete = Discipline::where('id', $id)->first();
        if ($evaluationDelete) {
            $tgl = $evaluationDelete->periode;
            $user_id = $evaluationDelete->user_id;

            $evaluationDelete->delete();
            \App\Services\EvaluationCalculatorService::recalculate($user_id, $tgl);

            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Berhasil menghapus kedisiplinan');
        } else {
            session()->flash('error', 'Gagal menghapus kedisiplinan: Data tidak ditemukan');
        }
    }
}
