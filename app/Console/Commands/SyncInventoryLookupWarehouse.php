<?php

namespace App\Console\Commands;

use App\Models\InventoryLookup;
use App\Models\SerialNumber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncInventoryLookupWarehouse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:sync-warehouse {--dry-run : Chỉ hiển thị kết quả mà không thực hiện cập nhật}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ warehouse_id trong inventory_lookup với warehouse_id trong serial_numbers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('Bắt đầu đồng bộ warehouse_id trong inventory_lookup...');
        
        if ($isDryRun) {
            $this->warn('CHẠY THỬ NGHIỆM - Không thực hiện cập nhật thực tế');
        }
        
        // Tìm các inventory_lookup có warehouse_id khác với serial_numbers
        $mismatchedRecords = DB::table('inventory_lookup')
            ->join('serial_numbers', 'inventory_lookup.sn_id', '=', 'serial_numbers.id')
            ->whereColumn('inventory_lookup.warehouse_id', '!=', 'serial_numbers.warehouse_id')
            ->select(
                'inventory_lookup.id as inventory_id',
                'inventory_lookup.sn_id',
                'inventory_lookup.warehouse_id as current_warehouse_id',
                'serial_numbers.warehouse_id as correct_warehouse_id',
                'serial_numbers.serial_code'
            )
            ->get();
        
        if ($mismatchedRecords->isEmpty()) {
            $this->info('✅ Tất cả warehouse_id đã đồng bộ!');
            return;
        }
        
        $this->info("Tìm thấy {$mismatchedRecords->count()} bản ghi cần đồng bộ:");
        
        // Hiển thị bảng kết quả
        $headers = ['Inventory ID', 'Serial Code', 'Current Warehouse', 'Correct Warehouse'];
        $rows = [];
        
        foreach ($mismatchedRecords as $record) {
            $rows[] = [
                $record->inventory_id,
                $record->serial_code,
                $record->current_warehouse_id,
                $record->correct_warehouse_id
            ];
        }
        
        $this->table($headers, $rows);
        
        if ($isDryRun) {
            $this->info('Đây là kết quả chạy thử nghiệm. Sử dụng --dry-run=false để thực hiện cập nhật thực tế.');
            return;
        }
        
        // Xác nhận trước khi cập nhật
        if (!$this->confirm('Bạn có chắc chắn muốn cập nhật các bản ghi này?')) {
            $this->info('Hủy bỏ thao tác.');
            return;
        }
        
        // Thực hiện cập nhật
        $updatedCount = 0;
        foreach ($mismatchedRecords as $record) {
            $updated = DB::table('inventory_lookup')
                ->where('id', $record->inventory_id)
                ->update(['warehouse_id' => $record->correct_warehouse_id]);
            
            if ($updated) {
                $updatedCount++;
            }
        }
        
        $this->info("✅ Đã cập nhật thành công {$updatedCount} bản ghi!");
        
        // Kiểm tra lại sau khi cập nhật
        $remainingMismatched = DB::table('inventory_lookup')
            ->join('serial_numbers', 'inventory_lookup.sn_id', '=', 'serial_numbers.id')
            ->whereColumn('inventory_lookup.warehouse_id', '!=', 'serial_numbers.warehouse_id')
            ->count();
        
        if ($remainingMismatched == 0) {
            $this->info('🎉 Tất cả warehouse_id đã được đồng bộ hoàn toàn!');
        } else {
            $this->warn("⚠️  Vẫn còn {$remainingMismatched} bản ghi chưa đồng bộ. Vui lòng kiểm tra lại.");
        }
    }
}
