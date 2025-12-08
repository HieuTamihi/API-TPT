<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class filterMonth extends Component
{
    /**
     * Create a new component instance.
     */
    public $name;
    public $title;
    public $date_start;
    public $date_end;
    public $button;
    public function __construct($name = null, $title = null, $date_start = null, $date_end = null, $button = null,)
    {
        $this->name = $name;
        $this->title = $title;
        $this->date_start = $date_start;
        $this->date_end = $date_end;
        $this->button = $button;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.filter-month');
    }
}
