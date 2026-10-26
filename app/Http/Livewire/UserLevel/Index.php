<?php

namespace App\Http\Livewire\UserLevel;

use App\Models\UserLevel;
use Livewire\Component;

class Index extends Component
{
    public $modal = 'level_user_modal';

    public $level, $tunjangan, $persentase_kinerja, $persentase_kedisiplinan;

    public $is_edit = false;

    public $_id;

    public $user_level;

    protected $listeners = [
        'edit',
        'delete'
    ];

    protected $rules = [
        'level' => 'required|string',
        'tunjangan' => 'required|numeric|between:1,9999999999'
    ];

    // protected $messages = [
    //     'name.required' => 'This image cannot be empty.',
    //     'role.required' => 'This image cannot be empty.',
    // ];

    // public function mount()
    // {
    //     $this->user_level = Department::ROLE_ACCESS;
    // }

    public function render()
    {
        return view('livewire.user-level.index')->layoutData([
            'title' => 'User Level'
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
        $this->level = null;
        $this->tunjangan = null;
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        if ($this->is_edit) {
            $data = UserLevel::where('id', $this->_id)->update([
                'level' => $this->level,
                'tunjangan' => $this->tunjangan,
                'persentase_kinerja' => $this->persentase_kinerja,
                'persentase_kedisiplinan' => $this->persentase_kedisiplinan
            ]);
        } else {
            $data = UserLevel::create([
                'level' => $this->level,
                'tunjangan' => $this->tunjangan,
                'persentase_kinerja' => $this->persentase_kinerja,
                'persentase_kedisiplinan' => $this->persentase_kedisiplinan
            ]);
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Berhasil menyimpan User Level');
        } else {
            session()->flash('error', 'Gagal menyimpan User Level');
        }
    }

    public function fixMenu($menu_access)
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

    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;
        $user_level = UserLevel::where('id', $data['id'])->first();
        $this->_id = $user_level['id'];
        $this->level = $user_level['level'];
        $this->tunjangan = $user_level['tunjangan'];
        $this->persentase_kinerja = $user_level['persentase_kinerja'];
        $this->persentase_kedisiplinan = $user_level['persentase_kedisiplinan'];
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = UserLevel::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Berhasil menghapus user level');
        } else {
            session()->flash('error', 'Gagal menghapus user level');
        }
    }
}
