<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SearchFilter extends Component
{
    public $keywords;
    public $filters;
    public $filtersTime;

    public function __construct($keywords = '', $filters = [], $filtersTime = [])
    {
        $this->keywords = $keywords;
        $this->filters = $filters;
        $this->filtersTime = $filtersTime;
    }

    public function render()
    {
        return view('components.search-filter');
    }
}
