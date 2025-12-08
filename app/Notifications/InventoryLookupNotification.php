<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InventoryLookupNotification extends Notification
{
    use Queueable;
    private $inventoryLookup;
    private $message;
    /**
     * Create a new notification instance.
     */
    public function __construct($inventoryLookup, $message)
    {
        $this->inventoryLookup = $inventoryLookup;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        // Nếu không có serial thì không tạo thông báo
        if (empty($this->inventoryLookup->serialNumber?->serial_code)) {
            return null; // Laravel sẽ bỏ qua không ghi vào DB
        }
        // Trả dữ liệu lưu trong bảng `notifications`
        return [
            'inventoryLookup_id' => $this->inventoryLookup->id,
            'serial_code' => $this->inventoryLookup->serialNumber->serial_code,
            'warranty_date' => $this->inventoryLookup->warranty_date,
            'message' => $this->message,
            'storage_duration' => $this->inventoryLookup->storage_duration,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
