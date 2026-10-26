<?php

namespace App\View\Components\Forms;

use Illuminate\Support\Str;
use Illuminate\View\Component;

class Checkbox extends Component
{
    public $value;

    public $label_id;

    public $label;

    public $disabled;

    public function __construct(
        $value,
        $label,
        $disabled = null
    ) {
        $this->value = $value;
        $this->disabled = $disabled;
        $this->label_id = Str::lower(Str::random(4)).'-'.Str::slug($label).'-'.rand(11111111, 99999999);
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.forms.checkbox');
    }
}
