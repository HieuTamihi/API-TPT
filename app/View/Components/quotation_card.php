<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class quotation_card extends Component
{
    public $quotation;
    public $quotationServices;
    public $terms;
    /**
     * Create a new component instance.
     */
    public function __construct($quotation, $quotationServices, $terms = null)
    {
        $this->quotation = $quotation;
        $this->quotationServices = $quotationServices;
        $this->terms = $terms;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string

    {
        return view('components.quotation_card');
    }
}
