<?php

namespace App\Console\Commands;

use App\Models\warrantyLookup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateWanrratyStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warranty:update-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Lấy ngày hiện tại
        $today = Carbon::now();

        // Lấy tất cả bản ghi đang trong trạng thái "đang bảo hành"
        $records = WarrantyLookup::all();

        foreach ($records as $record) {
            $status = 0; // Mặc định là còn bảo hành

            // Kiểm tra bảo hành thông thường
            if ($record->warranty_expire_date && $today->greaterThanOrEqualTo($record->warranty_expire_date)) {
                $status = 1; // Hết bảo hành
            }

            // Kiểm tra bảo hành dịch vụ
            if ($record->return_date && $record->service_warranty_expired) {
                $returnDate = Carbon::parse($record->return_date);
                $serviceExpiredDate = Carbon::parse($record->service_warranty_expired);
                
                if ($today->between($returnDate, $serviceExpiredDate)) {
                    $status = 2; // Đang trong thời gian bảo hành dịch vụ
                } else {
                    $status = 1; // Hết hạn bảo hành dịch vụ
                }
            }

            // Cập nhật status một lần duy nhất
            $record->update(['status' => $status]);

            // Lọc ra các bản ghi có cùng sn_id
            $snIdRecords = WarrantyLookup::where('sn_id', $record->sn_id)->get();

            // Kiểm tra nếu có bất kỳ bản ghi nào hết hạn bảo hành
            $expired = false;
            $warranties = []; // Mảng để lưu các tên bảo hành hết hạn

            // Duyệt qua các bản ghi có cùng sn_id
            foreach ($snIdRecords as $snIdRecord) {
                if ($snIdRecord->warranty_expire_date && $today->greaterThanOrEqualTo($snIdRecord->warranty_expire_date)) {
                    // Nếu bảo hành hết hạn, thêm tên bảo hành vào mảng và đánh dấu hết hạn
                    $expired = true;
                    $warranties[] = $snIdRecord->name_warranty;
                }
            }

            // Nối tên bảo hành hết hạn
            $nameStatus = implode(', ', $warranties) . ' hết bảo hành';

            // Nếu có bảo hành hết hạn, cập nhật trạng thái của tất cả bản ghi có cùng sn_id
            if ($expired) {
                WarrantyLookup::where('sn_id', $record->sn_id)
                    ->update(['name_status' => $nameStatus]);
            } else {
                // Nếu không có bảo hành hết hạn, thì cập nhật trạng thái là "Còn bảo hành"
                $record->update(['name_status' => "Còn bảo hành"]);
            }
        }

        $this->info('Đã cập nhật tình trạng bảo hành.');
        DB::disconnect();
    }
}
