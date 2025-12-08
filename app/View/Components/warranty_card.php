<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class warranty_card extends Component
{
    public $productExports;
    public $export;
    /**
     * Create a new component instance.
     */
    public function __construct($productExports = null, $export = null)
    {
        //
        $this->productExports = $productExports;
        $this->export = $export;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.warranty_card');
    }
}
