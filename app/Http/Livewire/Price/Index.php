<?php

namespace App\Http\Livewire\Price;

use Livewire\Component;
use App\Models\PriceCanteen;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Rules\UniqueActiveCanteenPrice;

class Index extends Component
{
    public $modal = 'price_modal';


    public $is_edit = false;

    public $_id;

    public $name, $prices, $status;

    protected $listeners = [
        'edit',
        'delete'
    ];

    protected $rules = [
        'name' => 'required|string',
        'prices' => 'required',
    ];

    public function rules()
    {
        $rules = [
            'name' => 'required|string',
            'prices' => 'required',
            'status' => [
                'required',
                Rule::unique('price_canteens', 'status')->where(function ($query) {
                    return $query->where('status', 'active');
                }),
            ],
        ];

        return $rules;
    }


    public function render()
    {
        return view('livewire.price.index')->layoutData([
            'title' => 'Price'
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
        $this->name = null;
        $this->prices = null;
        $this->status = null;
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();

        if ($this->is_edit) {
            $data = PriceCanteen::where('id', $this->_id)->update([
                'name' => $this->name,
                'prices' => $this->prices,
                'status' => $this->status,

            ]);
        } else {
            $data = PriceCanteen::create([
                'name' => $this->name,
                'prices' => $this->prices,
                'status' => $this->status,

            ]);
        }

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved PriceCanteen');
        } else {
            session()->flash('error', 'Failed to saved PriceCanteen');
        }
    }


    public function edit($data)
    {
        $this->resetErrorBag();
        $this->is_edit = true;
        $price = PriceCanteen::where('id', $data['id'])->first();
        $this->_id = $price['id'];
        $this->name = $price['name'];
        $this->prices = $price['prices'];
        $this->status = $price['status'];

        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function delete($data)
    {
        $this->resetErrorBag();

        $data = PriceCanteen::where('id', $data['id'])->delete();

        if ($data) {
            $this->emit('re_render_table');
            $this->closeModal();
            session()->flash('success', 'Successfully saved PriceCanteen');
        } else {
            session()->flash('error', 'Failed to saved PriceCanteen');
        }
    }
}
