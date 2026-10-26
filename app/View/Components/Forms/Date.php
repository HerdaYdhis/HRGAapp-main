<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

class Date extends Component
{
    public $name;
    public $model;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name, $model)
    {
        $this->model = $model;
        $this->name = $name;
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.forms.date');
    }
}
