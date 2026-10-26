<?php

namespace App\View\Components\Buttons;

use Illuminate\View\Component;

class Submit extends Component
{
    public $target;

    public $label;

    public function __construct($target, $label)
    {
        $this->target = $target;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.buttons.submit');
    }
}
