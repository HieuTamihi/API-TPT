<?php

namespace App\Console\Commands;

use App\Models\InventoryLookup;
use App\Models\Notification;
use App\Models\ProductExport;
use App\Models\ProductImport;
use App\Models\SerialNumber;
use App\Models\User;
use App\Models\warrantyLookup;
use App\Notifications\InventoryLookupNotification;
use App\Notifications\ReceiNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateInventoryStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:update-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật thời gian tồn kho của Inventory Lookup mỗi ngày';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Lấy tất cả các bản ghi trong InventoryLookup
        $records = InventoryLookup::all();

        foreach ($records as $record) {
            // Tính thời gian tồn kho
            $receivedDate = Carbon::parse($record->import_date); // Ngày nhập kho
            $storageDuration = $receivedDate->diffInDays(Carbon::now()); // Tính số ngày tồn kho

            // Cập nhật thời gian tồn kho
            $record->update(['storage_duration' => $storageDuration]);

            // Kiểm tra lần bảo trì đầu tiên hoặc các lần sau
            if ($storageDuration >= 90 && !$record->warranty_date) {
                // Lần bảo trì đầu tiên
                $record->status = 1;
                $record->save();
                // Gửi thông báo lần bảo trì đầu tiên
                $message = "tới hạn bảo trì";
                $this->notifyStatusChange($record, $message);
            } else if ($record->warranty_date) {
                // Các lần bảo trì tiếp theo
                $nextMaintenanceDate = Carbon::parse($record->warranty_date)->addDays(90);

                if (Carbon::now()->greaterThanOrEqualTo($nextMaintenanceDate)) {
                    // Nếu đã tới thời gian bảo trì tiếp theo
                    $record->status = 1;
                    $record->save();
                    // Gửi thông báo lần bảo trì tiếp theo
                    $message = "tới hạn bảo trì";
                    $this->notifyStatusChange($record, $message);
                }
            } else {
                $record->status = 0;
                $record->save();
            }
        }

        //cập nhật tồn kho và export_id bảo hành
        // $inventoryItems = InventoryLookup::whereNotNull('sn_id')->get();

        // foreach ($inventoryItems as $lookup) {
        //     // Luôn tìm và cập nhật import_id
        //     $import = ProductImport::where('sn_id', $lookup->sn_id)->orderByDesc('id')->first();
        //     if ($import) {
        //         $lookup->import_id = $import->import_id; // hoặc $import->id nếu dùng id chính
        //     }

        //     // Kiểm tra serial
        //     $serial = SerialNumber::find($lookup->sn_id);
        //     if (!$serial) {
        //         continue; // Không tồn tại SN thì bỏ qua
        //     }

        //     // Nếu đã xuất rồi thì tồn kho = 0
        //     if ($serial->status == 2) {
        //         $lookup->remaining_quantity = 0;
        //     } else {
        //         $totalImport = ProductImport::where('sn_id', $lookup->sn_id)->sum('quantity');
        //         $totalExport = ProductExport::where('sn_id', $lookup->sn_id)->sum('quantity');
        //         $lookup->remaining_quantity = max($totalImport - $totalExport, 0);
        //     }

        //     $lookup->save();
        // }

        // $warranties = warrantyLookup::whereNotNull('sn_id')->get();
        // foreach ($warranties as $warranty) {
        //     $export = ProductExport::where('sn_id', $warranty->sn_id)->orderByDesc('id')->first();
        //     if ($export) {
        //         $warranty->export_id = $export->export_id;
        //         $warranty->save();
        //     }
        // }

        //Cập nhật tồn kho khi chuyển kho
        // InventoryLookup::where('sn_id', 1458)->update(['remaining_quantity' => 1]);

        //xóa serial
        // $ids = [2857];

        // $count = SerialNumber::whereIn('id', $ids)->delete();

        $this->info('Đã cập nhật thời gian tồn kho cho tất cả các sản phẩm.');
        DB::disconnect();
    }
    private function notifyStatusChange($record, $message)
    {
        // Lấy tất cả người dùng không có quyền 'dichvu'
        $users = User::whereDoesntHave('permissions', function ($query) {
            $query->where('name', 'dichvu');
        })->get();

        foreach ($users as $user) {
            // Kiểm tra nếu người dùng không có quyền 'dichvu' và chưa nhận thông báo này
            if (!$user->hasPermissionTo('dichvu')) {
                // Gửi thông báo nếu chưa tồn tại thông báo tương tự cho user
                $existingNotification = $user->notifications()
                    ->where('type', InventoryLookupNotification::class)
                    ->where('data->inventoryLookup_id', $record->id)
                    ->where('data->message', $message)
                    ->where('data->warranty_date', $record->warranty_date)
                    ->exists();

                if (!$existingNotification) {
                    // Tạo thông báo mới
                    $user->notify(new InventoryLookupNotification($record, $message));
                }
            }
        }
    }
}
