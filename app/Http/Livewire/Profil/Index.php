<?php

namespace App\Http\Livewire\Profil;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class Index extends Component
{
    use WithFileUploads;

    public $modal = 'user_modal';

    public $name, $email, $no_wa, $password, $address, $avatar;

    public $is_edit = false;

    public $_id;

    public $profil;



    protected $listeners = [
        'edit',
        'delete'
    ];

    protected function rules()
    {
        return [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(auth()->user()->id),
            ],
            'no_wa' => 'required',
            'password' => 'required',
            'address' => 'required',
            'avatar' => 'nullable|image|max:2048',
        ];
    }


    public function render()
    {
        return view('livewire.profil.index')->layoutData([
            'title' => 'Profile'
        ]);
    }


    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset_form();

        // Mengisi nilai inputan dengan data profil saat ini
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->no_wa = $user->no_wa;
        $this->password = null;
        $this->address = $user->address;

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
        $this->name = null;
        $this->email = null;
        $this->no_wa = null;
        $this->password = null;
        $this->address = null;
        $this->avatar = null;
        $this->is_edit = false;
    }

    // ...
    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        $user = auth()->user();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->no_wa = $this->no_wa;
        $user->password = Hash::make($this->password);
        $user->address = $this->address;
        // $user->department_id = $this->department_id;

        if ($this->avatar) {
            // Hapus gambar lama (jika ada)
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan gambar baru
            $newImagePath = $this->avatar->store('photos', 'public');
            $user->avatar = $newImagePath;
        }

        $user->save();

        $this->emit('re_render_table');
        $this->closeModal();
        session()->flash('success', 'Profil berhasil diperbarui.');
    }
    // ...


}
