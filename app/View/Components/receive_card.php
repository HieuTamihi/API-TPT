<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class receive_card extends Component
{
    public $receivedProducts;
    public $receiving;
    /**
     * Create a new component instance.
     */
    public function __construct($receivedProducts = null, $receiving = null)
    {
        //
        $this->receivedProducts = $receivedProducts;
        $this->receiving = $receiving;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.receive_card');
    }
}
