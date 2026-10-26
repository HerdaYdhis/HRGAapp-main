<?php

namespace App\Http\Livewire\WarningLetter;

use Livewire\WithFileUploads;
use App\Models\WarningLetter;
use App\Models\EvaluationResult;
use App\Models\DisciplineResult;
use App\Models\User;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportWarningLetter;
use App\Exports\ExportWarningLetterTemplate;
use App\Imports\ImportWarningLetter;

class Index extends Component
{
    use WithFileUploads;

    public $modal = 'Discipline_modal';
    /** @var int|float|null */
    public $sp1;
    /** @var int|float|null */
    public $sp2;
    /** @var int|float|null */
    public $sp3;
    /** @var int|float|null */
    public $nilai;
    /** @var string|null */
    public $periode;
    /** @var int|string|null */
    public $user_id;
    /** @var int|float|string|null */
    public $sp;

    public $is_edit = false;

    /** @var int|string|null */
    public $_id;
    public $is_upload_excel = false;
    /** @var mixed */
    public $user_select;
    /** @var mixed */
    public $periode_select;
    public $list_user = false;
    /** @var mixed */
    public $warningLetter;
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
        'sp1' => 'between:0,100',
        'sp2' => 'between:0,100',
        'sp3' => 'between:0,100',
        'periode' => 'required|date_format:Y-m',
    ];

    // protected $messages = [
    //     'name.required' => 'This image cannot be empty.',
    //     'role.required' => 'This image cannot be empty.',
    // ];

    public function mount()
    {
        // $test = Evaluation::with('performance')->select('evaluations.*')->get();
        // dd($test);
        $this->userdata();
    }

    public function userdata()
    {
        $userdata = User::get();
        return $this->userdata = $userdata;
    }


    public function render()
    {
        return view('livewire.warning-letter.index')->layoutData([
            'title' => 'Surat Peringatan'
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
        $this->user_id = "";
        $this->periode = "";
        $this->sp = "";
        $this->sp1 = "";
        $this->sp2 = "";
        $this->sp3 = "";
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

    public function import()
    {
        try {
            $this->validate([
                'excelFile' => 'required|mimes:xls,xlsx',
            ]);

            $import = new ImportWarningLetter;
            Excel::import($import, $this->excelFile);

            $count = $import->getImportedCount();
            session()->flash('success', "Berhasil mengimpor {$count} data surat peringatan.");
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan impor: ' . $e->getMessage());
        }
        $this->closeModal();
        $this->emit('re_render_table');
    }

    public function download()
    {
        return Excel::download(new ExportWarningLetter, 'surat_peringatan.xlsx');
    }

    public function template()
    {
        return Excel::download(new ExportWarningLetterTemplate, 'template_surat_peringatan.xlsx');
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        $tgl = $this->periode;
        if ($this->sp == 20) {
            $input_sp1 = 1;
            $input_sp2 = 0;
            $input_sp3 = 0;
        } elseif ($this->sp == 30) {
            $input_sp1 = 0;
            $input_sp2 = 1;
            $input_sp3 = 0;
        } elseif ($this->sp == 50) {
            $input_sp1 = 0;
            $input_sp2 = 0;
            $input_sp3 = 1;
        } else {
            $input_sp1 = 0;
            $input_sp2 = 0;
            $input_sp3 = 0;
        }

        $countNilai = \App\Services\EvaluationCalculatorService::calculateSPScore($input_sp1, $input_sp2, $input_sp3);

        if ($this->is_edit) {
            WarningLetter::where('id', $this->_id)->update([
                'user_id' => $this->user_id,
                'sp1' => $input_sp1,
                'sp2' => $input_sp2,
                'sp3' => $input_sp3,
                'nilai' => $countNilai,
                'periode' => $this->periode
            ]);
        } else {
            $check = WarningLetter::where('user_id', $this->user_id)->where('periode', $tgl)->first();
            if ($check) {
                session()->flash('error', 'Nilai surat peringatan sudah terisi nilai pada periode ini');
                $this->closeModal();
                return;
            }

            WarningLetter::create([
                'user_id' => $this->user_id,
                'sp1' => $input_sp1,
                'sp2' => $input_sp2,
                'sp3' => $input_sp3,
                'nilai' => $countNilai,
                'periode' => $this->periode
            ]);
        }

        \App\Services\EvaluationCalculatorService::recalculate($this->user_id, $tgl);

        $this->reset_form();
        $this->emit('re_render_table');
        $this->closeModal();
        session()->flash('success', 'Berhasil menyimpan nilai surat peringatan');
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
        $this->is_edit = true;

        $warningLetter = WarningLetter::where('id', $data['id'])->first();
        $this->_id = $warningLetter['id'];
        $this->user_id = $warningLetter['user_id'];
        $this->periode = $warningLetter['periode'];

        if ($warningLetter['sp1'] == 1) {
            $this->sp = 20;
        } elseif ($warningLetter['sp2'] == 1) {
            $this->sp = 30;
        } elseif ($warningLetter['sp3'] == 1) {
            $this->sp = 50;
        } else {
            $this->sp = 0;
        }

        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete(array $data)
    {
        $this->resetErrorBag();
        $id = $data['id'];
        $warningLetterDelete = WarningLetter::where('id', $id)->first();
        if ($warningLetterDelete) {
            $tgl = $warningLetterDelete->periode;
            $user_id = $warningLetterDelete->user_id;

            $warningLetterDelete->delete();
            \App\Services\EvaluationCalculatorService::recalculate($user_id, $tgl);

            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Berhasil menghapus surat peringatan');
        } else {
            session()->flash('error', 'Gagal menghapus: Data tidak ditemukan');
        }
    }
}
