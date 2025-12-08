<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationComponent extends Component
{
    public $notifications;

    public function mount()
    {
        // Lấy thông báo của người dùng đã đăng nhập
        $this->notifications = Auth::user()->notifications;
    }

    public function render()
    {
        return view('livewire.notification-component');
    }
}
