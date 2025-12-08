<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class view_mini extends Component
{
    public $guestOrProvider;
    public $users;
    public $name;
    public $data;

    /**
     * Create a new component instance.
     */
    public function __construct($guestOrProvider = null, $users = null, $name = null, $data)
    {
        $this->guestOrProvider = $guestOrProvider;
        $this->users = $users;
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.view_mini');
    }
}
