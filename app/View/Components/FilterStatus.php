<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\View\View;

class FilterStatus extends Component
{
    /**
     * Properties for the filter.
     */
    public $name;
    public $title;
    public $button;
    public $filters;

    /**
     * Create a new component instance.
     */
    public function __construct($name = null, $title = null, $button = null, $filters = [])
    {
        $this->name = $name;
        $this->title = $title;
        $this->button = $button;
        $this->filters = $filters;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.filter-status');
    }
}
