<?php

namespace App\Http\Livewire\Borrowing;

use Livewire\Component;
use App\Models\Meetroom;
use App\Models\BorrowingRoom;
use Illuminate\Validation\Rule;

class Index extends Component
{

    public $modal = 'borrowing_modal';

    public $is_edit = false;

    public $_id;

    public $user_id, $objective,  $meetroom_id, $start_date, $start_time, $done_date, $done_time;
    public $meetingroom, $note;


    protected $listeners = [
        'edit',
        'delete'
    ];

    protected $rules = [
        'objective' => 'required',
        'start_date' => 'required',
        'start_time' => 'required',
        'done_date' => 'required',
        'done_time' => 'required',
        'meetroom_id' => 'required',
    ];

    public function mount()
    {
        $this->meetingroom = MeetRoom::get();
    }

    public function openModal()
    {
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
        $this->objective = null;
        $this->start_date = null;
        $this->start_time = null;
        $this->done_date = null;
        $this->done_time = null;
        $this->meetroom_id = null;
        $this->note = null;
    }

    public function submit()
    {

        // dd($this->all());

        $date_parts = explode(" to ", $this->start_date);
        $this->start_date = $date_parts[0];
        $this->done_date = isset($date_parts[1]) ? $date_parts[1] : $this->start_date;

        $this->resetErrorBag();
        $this->validate();


        // Lakukan validasi
        $this->validate([
            'start_time' => [
                'required',
                // 'date_format:H:i', // Format waktu harus jam:menit (HH:mm)
            ],
            'done_time' => [
                'required',
                'after_or_equal:start_time'
                // 'date_format:H:i', // Format waktu harus jam:menit (HH:mm)
            ],
            'meetroom_id' => [
                'required',
            ],
            'objective' => [
                'required',
                'string',
                // Rule::unique('borrowing_rooms')->ignore($this->_id),
            ],
        ]);

        // Lakukan validasi tambahan untuk memeriksa apakah ruangan masih dipesan pada waktu yang dimaksud
        $overlap = BorrowingRoom::where('meetroom_id', $this->meetroom_id)
            ->where('id', '!=', $this->_id)
            ->where(function ($query) {
                $query->where('start_date', '<=', $this->done_date)
                    ->where('done_date', '>=', $this->start_date)
                    ->where(function ($innerQuery) {
                        $innerQuery->where(function ($q) {
                            $q->whereTime('start_time', '<', $this->done_time)
                                ->whereTime('done_time', '>', $this->start_time);
                        })
                            ->orWhere(function ($q) {
                                $q->whereTime('start_time', '<', $this->start_time)
                                    ->whereTime('done_time', '>', $this->done_time);
                            });
                    });
            })
            ->exists();

        if ($overlap) {
            session()->flash('error', 'Ruangan sudah dipesan pada waktu yang dipilih.');
            return;
        }

        if ($this->is_edit) {
            $data = BorrowingRoom::where('id', $this->_id)->update([
                'objective' => $this->objective,
                'start_date' => $this->start_date,
                'start_time' => $this->start_time,
                'done_date' => $this->done_date,
                'done_time' => $this->done_time,
                'meetroom_id' => $this->meetroom_id,
                'note' => $this->note,
            ]);
            // return redirect(route('pinjamruangan'));
            // $this->reset_form();
        } else {
            $data = BorrowingRoom::create([
                'user_id' => auth()->user()->id,
                'objective' => $this->objective,
                'start_date' => $this->start_date,
                'start_time' => $this->start_time,
                'done_date' => $this->done_date,
                'done_time' => $this->done_time,
                'meetroom_id' => $this->meetroom_id,
                'note' => $this->note,
            ]);
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved MeetingRoom');
        } else {
            session()->flash('error', 'Failed to saved MeetingRoom');
        }
    }

    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;

        $this->_id = $data['id'];

        $this->objective = $data['objective'];
        $this->start_date = $data['start_date'];
        $this->done_date = $data['done_date'];
        $this->start_time = $data['start_time'];
        $this->done_time = $data['done_time'];
        $this->meetroom_id = $data['meetroom_id'];
        $this->note = $data['note'];


        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = BorrowingRoom::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved MeetingRoom');
        } else {
            session()->flash('error', 'Failed to saved MeetingRoom');
        }
    }

    public function render()
    {
        return view('livewire.borrowing.index')->layoutData([
            'title' => 'Peminjaman Ruang Meeting'
        ]);;
    }
}
