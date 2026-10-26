<?php

namespace App\Http\Livewire\Performances;

use Livewire\WithFileUploads;
use App\Models\Performance;
use App\Models\User;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportIndikator;
use App\Exports\ExportIndikatorTemplate;
use App\Imports\ImportIndikator;
use Illuminate\Support\Facades\Session;


class Index extends Component
{
    use WithFileUploads;
    public $modal = 'performance_modal';
    public $is_upload_excel = false;
    /** @var string|null */
    public $indikator;
    /** @var int|float|null */
    public $bobot;

    public $is_edit = false;

    /** @var int|string|null */
    public $_id;
    /** @var int|string|null */
    public $user_id;

    /** @var mixed */
    public $performances;
    /** @var mixed */
    public $userdata;
    /** @var mixed */
    public $othersVisible;
    public $select_user = false;
    public $user_name = '';
    /** @var mixed */
    public $excelFile;


    protected $listeners = [
        'edit',
        'delete',
        'input_nilai'
    ];

    protected $rules = [
        'indikator' => 'required|string',
        'bobot' => 'required|numeric|between:0,100'
    ];

    // protected $messages = [
    //     'name.required' => 'This image cannot be empty.',
    //     'role.required' => 'This image cannot be empty.',
    // ];


    public function back()
    {
        $this->othersVisible = null;
        $this->select_user = false;
    }

    public function input_nilai(array $data)
    {
        $this->othersVisible = $data['id'];
        $this->select_user = true;
        session()->put('sendUserId',  $this->othersVisible);
        $userdata = User::where('id', $data['id'])->first();
        $this->user_name = $userdata->name;
    }
    public function mount()
    {
        // $test = Evaluation::with('performance')->select('evaluations.*')->get();
        // dd($test);
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


    public function download()
    {
        $user = User::where('id', $this->othersVisible)->first();
        return Excel::download(new ExportIndikator, 'Indikator-' . $user->name . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new ExportIndikatorTemplate, 'template_list_kinerja.xlsx');
    }

    public function import()
    {
        try {
            $this->validate([
                'excelFile' => 'required|mimes:xls,xlsx',
            ]);

            $import = new ImportIndikator($this->othersVisible);
            Excel::import($import, $this->excelFile);

            $count = $import->getImportedCount();
            session()->flash('success', "Berhasil mengimpor {$count} indikator kinerja.");
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan impor: ' . $e->getMessage());
        }

        $this->closeModal();
        $this->emit('re_render_table');
    }

    public function render()
    {
        return view('livewire.performances.index')->layoutData([
            'title' => 'Kinerja'
        ]);
    }

    public function openModal()
    {
        $this->is_upload_excel = false;
        $this->is_edit = false;
        $this->resetErrorBag();
        $this->reset_form();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function openModalExcel()
    {
        $this->is_upload_excel = true;
        $this->is_edit = false;
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
        $this->is_edit = false;
        $this->_id = null;
        $this->indikator = null;
        $this->bobot = null;
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        if ($this->is_edit) {
            $nilai_indikator = Performance::where('id', $this->_id)->first();
            $total = Performance::where('user_id', $this->othersVisible)->get();
            $nilai = 0;
            foreach ($total as $key => $value) {
                $nilai += $value->bobot;
            }
            $check = ($nilai - $nilai_indikator->bobot) + $this->bobot;
            if ($check > 100) {
                $this->closeModal();
                session()->flash('error', 'Total bobot melebihi 100');
            } else {
                $data = Performance::where('id', $this->_id)->update([
                    'indikator' => $this->indikator,
                    'bobot' => $this->bobot,
                    'user_id' => $this->othersVisible
                ]);
                if ($data) {
                    $this->emit('re_render_table');
                    $this->closeModal();
                    session()->flash('success', 'Berhasil menyimpan data kinerja');
                } else {
                    session()->flash('error', 'Gagal menyimpan data kinerja');
                }
            }
        } else {
            $total = Performance::where('user_id', $this->othersVisible)->sum('bobot');
            $check = $total + $this->bobot;
            if ($check > 100) {
                $this->closeModal();
                session()->flash('error', 'Total bobot melebihi 100');
            } else {
                $data = Performance::create([
                    'indikator' => $this->indikator,
                    'bobot' => $this->bobot,
                    'user_id' => $this->othersVisible
                ]);
                if ($data) {
                    $this->emit('re_render_table');
                    $this->closeModal();
                    session()->flash('success', 'Berhasil menyimpan data kinerja');
                } else {
                    session()->flash('error', 'Gagal menyimpan data kinerja');
                }
            }
        }
    }

    public function fixMenu(mixed $menu_access)
    {
        $return = [];
        $fix = collect($menu_access)->map(function ($item) {
            return collect($item)->filter(function ($item, $value) {
                return $item;
            })->toArray();
        })->toArray();
        foreach ($fix as $key => $value) {
            if (count($value) > 0) {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    public function edit(array $data)
    {
        $this->is_upload_excel = false;
        $this->resetErrorBag();
        $this->is_edit = true;
        $performances = Performance::where('id', $data['id'])->first();
        $this->_id = $performances['id'];
        $this->indikator = $performances['indikator'];
        $this->bobot = $performances['bobot'];
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete(array $data)
    {
        $this->resetErrorBag();

        $data = Performance::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Berhasil menghapus data kinerja');
        } else {
            session()->flash('error', 'Gagal menghapus data kinerja');
        }
    }
}
