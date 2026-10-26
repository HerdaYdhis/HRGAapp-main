<?php

namespace App\Http\Livewire\EvaluationResult;

use App\Models\EvaluationResult;
use App\Models\Performance;
use App\Models\User;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportResultSelf;

class IndexSelf extends Component
{
    public $modal = 'evaluation_modal';
    public $nilai, $user_id, $periode, $performance_id;

    public $is_edit = false;

    public $_id;

    public $select_user = false;
    // public $performance;
    // public $userdata;

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
        return view('livewire.evaluation-result.index-self')->layoutData([
            'title' => 'Evaluation'
        ]);
    }

    public function download()
    {
        $user = User::where('id', auth()->user()->id)->first();
        return Excel::download(new ExportResultSelf, 'Rekap tunjangan ' . $user->name . '.xlsx');
    }
}
