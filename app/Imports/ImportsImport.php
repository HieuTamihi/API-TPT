<?php

namespace App\Imports;

use App\Models\Imports;
use App\Models\ProductImport;
use App\Models\InventoryLookup;
use App\Models\Product;
use App\Models\Providers;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class ImportsImport implements ToCollection, WithHeadingRow
{
    /**
     * Xử lý tập hợp các hàng từ file Excel
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. Bỏ qua nếu dữ liệu dòng quan trọng bị thiếu
            if (!isset($row['ma_phieu']) || !isset($row['ma_hang'])) {
                continue;
            }

            // 2. Tìm ID các đối tượng liên quan (Tránh lỗi nếu nhập tên sai)
            // Giả sử Excel có cột: ma_hang, ma_ncc, ma_kho
            $product = Product::where('product_code', $row['ma_hang'])->first();
            
            // Nếu không tìm thấy sản phẩm, bỏ qua dòng này
            if (!$product) continue;

            $provider = Providers::where('provider_code', $row['ma_ncc'] ?? '')->first();
            $warehouse = Warehouse::where('warehouse_code', $row['ma_kho'] ?? '')->first();

            // Giá trị mặc định nếu không tìm thấy
            $providerId = $provider ? $provider->id : 1; 
            $warehouseId = $warehouse ? $warehouse->id : 1;
            $userId = Auth::id() ?? 1;

            // Xử lý ngày tháng (Excel trả về số hoặc chuỗi)
            try {
                $dateCreate = isset($row['ngay_lap']) 
                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['ngay_lap']) 
                    : now();
            } catch (\Exception $e) {
                $dateCreate = now();
            }

            // 3. Tạo hoặc Lấy Phiếu Nhập (Imports)
            // Logic: Nếu mã phiếu đã tồn tại thì lấy ID đó để thêm sản phẩm vào, chưa có thì tạo mới
            $import = Imports::firstOrCreate(
                ['import_code' => $row['ma_phieu']], 
                [
                    'provider_id' => $providerId,
                    'warehouse_id' => $warehouseId,
                    'user_id' => $userId,
                    'date_create' => $dateCreate,
                    'note' => $row['ghi_chu'] ?? 'Import từ Excel',
                    'contact_person' => $row['nguoi_lien_he'] ?? '',
                    'phone' => $row['sdt'] ?? '',
                    'address' => $row['dia_chi'] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // 4. Tạo Chi tiết nhập (ProductImport)
            $quantity = (int)($row['so_luong'] ?? 1);

            ProductImport::create([
                'import_id' => $import->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'sn_id' => 0, // Mặc định 0 cho hàng import số lượng lớn (không serial cụ thể)
                'note' => $row['ghi_chu_sp'] ?? null,
            ]);

            // 5. Cộng Tồn kho (InventoryLookup)
            InventoryLookup::create([
                'product_id' => $product->id,
                'sn_id' => 0,
                'provider_id' => $providerId,
                'warehouse_id' => $warehouseId,
                'import_date' => $dateCreate,
                'storage_duration' => 0,
                'status' => 1, // 1: Trong kho
                'remaining_quantity' => $quantity,
                'import_id' => $import->id,
                'note' => 'Import Excel',
            ]);
        }
    }
}