<?php

namespace App\View\Components\Charts;

use Illuminate\View\Component;

class Chart extends Component
{
    public $name;
    public $chart;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name, $chart)
    {
        $this->name = $name;
        $this->chart = $chart;
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.charts.chart');
    }
}
