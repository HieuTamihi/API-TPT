<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Receiving;
use App\Models\ReturnForm;
use App\Models\SerialNumber;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateReceivingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receiving:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status of receiving records daily based on date_created and conditions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now();

        // Các trạng thái để so sánh
        $messages = [
            '0' => 'tiếp nhận',
            '1' => 'chưa xử lý',
            '2' => 'quá hạn',
        ];

        // Tiếp nhận (< 3 ngày, state = 0)
        Receiving::whereIn('status', [1, 2])
            ->where('date_created', '>=', $today->copy()->subDays(3))
            ->get()
            ->each(function ($receiving) use ($messages) {
                $newState = 0;
                $receiving->update(['state' => 0]);
            });

        // Chưa xử lý (>= 3 ngày, state = 1)
        Receiving::whereIn('status', [1])
            ->where('date_created', '<', $today->copy()->subDays(3))
            ->where('date_created', '>=', $today->copy()->subDays(21))
            ->get()
            ->each(function ($receiving) use ($messages) {

                $newState = 1;
                $message = $messages[1];
                $receiving->update(['state' => 1]);
                // Gửi thông báo
                $this->notifyStateChange($receiving, $newState, $message);
            });
        Receiving::where('status', 2)
            ->where('date_created', '<', $today->copy()->subDays(3))
            ->where('date_created', '>=', $today->copy()->subDays(21))
            ->get()
            ->each(function ($receiving) use ($messages) {

                $newState = 1;
                $message = $messages[1];
                $receiving->update(['state' => 0]);
                // Gửi thông báo
                $this->notifyStateChange($receiving, $newState, $message);
            });

        // Quá hạn (>= 21 ngày, state = 2)
        Receiving::whereNotIn('status', [3, 4])
            ->where('date_created', '<', $today->copy()->subDays(21))
            ->get()
            ->each(function ($receiving) use ($messages) {
                $newState = 2;
                $message = $messages[2];
                $receiving->update(['state' => 2]);

                // Gửi thông báo
                $this->notifyStateChange($receiving, $newState, $message);
            });

        // Hoàn thành hoặc khách không đồng ý
        Receiving::whereIn('status', [3, 4])
            ->update(['state' => 0]);

        // Đồng bộ ngày lập phiếu của phiếu trả hàng với ngày đóng phiếu của phiếu tiếp nhận
        // $this->syncReturnFormDates();

        // $ids = [2646, 2645, 2644, 2643];
        // $updated = SerialNumber::whereIn('id', $ids)
        //     ->update(['status' => 4]);
        // $productReturns = \App\Models\ProductReturn::where('return_form_id', 12)
        //     ->take(count($ids))
        //     ->get();
        // if ($productReturns->count() < count($ids)) {
        //     $this->error('Không đủ dòng product_returns để cập nhật.');
        //     return;
        // }
        // foreach ($productReturns as $index => $return) {
        //     $return->replacement_serial_number_id = $ids[$index];
        //     $return->save();
        // }
    
        $this->info('Receiving statuses updated successfully.');
        DB::disconnect();
        return Command::SUCCESS;
    }

    private function notifyStateChange($receiving, $newState, $message)
    {
        $users = User::whereDoesntHave('permissions', function ($query) {
            $query->where('name', 'quankho');
        })->get();
        foreach ($users as $user) {
            // Kiểm tra xem thông báo đã tồn tại chưa
            $notificationExists = $user->notifications()
                ->where('type', \App\Notifications\ReceiNotification::class)
                ->where('data->receiving_id', $receiving->id)
                ->where('data->state', $newState)
                ->exists();
            // Chỉ gửi thông báo nếu chưa tồn tại
            if (!$notificationExists) {
                $user->notify(new \App\Notifications\ReceiNotification($receiving, $newState, $message));
            }
        }
    }

    /**
     * Đồng bộ ngày đóng phiếu của phiếu tiếp nhận với ngày lập phiếu của phiếu trả hàng
     */
    private function syncReturnFormDates()
    {
        // Lấy tất cả phiếu trả hàng có status = 1 (hoàn thành)
        $returnForms = ReturnForm::where('status', 1)
            ->with('reception')
            ->get();

        foreach ($returnForms as $returnForm) {
            if ($returnForm->reception) {
                // Cập nhật ngày đóng phiếu của phiếu tiếp nhận = ngày lập phiếu của phiếu trả hàng
                $returnForm->reception->update([
                    'closed_at' => $returnForm->date_created
                ]);
            }
        }

        $this->info('Synchronized receiving closed dates with return form dates.');
    }
}
