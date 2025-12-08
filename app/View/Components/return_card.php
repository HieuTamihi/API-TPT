<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class return_card extends Component
{
    public $returnForm;
    public $returnProducts;
    /**
     * Create a new component instance.
     */
    public function __construct($returnForm = null, $returnProducts = null)
    {
        $this->returnForm = $returnForm;
        $this->returnProducts = $returnProducts;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.return_card');
    }
}
