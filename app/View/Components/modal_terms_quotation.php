<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class modal_terms_quotation extends Component
{
    public $terms;
    /**
     * Create a new component instance.
     */
    public function __construct($terms = null)
    {
        $this->terms = $terms;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal_terms_quotation');
    }
}
