<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class Select extends Component
{
    public $datas;
    public $name;
    public $model;

    public function __construct(
        $datas,
        $value,
        $label,
        $name,
        $model
    ) {
        $this->name = $name;
        $this->model = $model;
        $this->datas = collect($datas)->pluck($label, $value)->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.forms.select');
    }
}
