<?php

namespace App\Imports;

use App\Models\Providers;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProvidersImport implements ToCollection
{
    public $duplicates = [];

    public function collection(Collection $rows)
    {
        $rows = $rows->skip(1); // Bỏ qua hàng tiêu đề
    
        // Lấy danh sách tất cả mã số thuế có trong database
        $existingTaxCodes = Providers::pluck('id', 'tax_code')->toArray();
    
        foreach ($rows as $row) {
            $tax_code = $row[6] ?? null;
    
            // Nếu tax_code không rỗng và đã tồn tại -> lưu vào danh sách trùng lặp
            if (!empty($tax_code) && isset($existingTaxCodes[$tax_code])) {
                $this->duplicates[] = [
                    'provider_id' => $existingTaxCodes[$tax_code], // Lấy ID nhà cung cấp trùng
                    'row_data' => $row // Lưu dữ liệu hàng trùng lặp
                ];
            } else {
                // Thêm vào database (không kiểm tra nếu mã số thuế rỗng)
                $provider = Providers::create([
                    'provider_code' => $row[0] ?? null,
                    'provider_name' => $row[1] ?? null,
                    'address' => $row[3] ?? null,
                    'contact_person' => $row[2] ?? null,
                    'phone' => $row[4] ?? null,
                    'email' => $row[5] ?? null,
                    'tax_code' => $tax_code,
                    'note' => $row[7] ?? null,
                ]);
    
                // Cập nhật vào danh sách đã kiểm tra nếu tax_code không rỗng
                if (!empty($tax_code)) {
                    $existingTaxCodes[$tax_code] = $provider->id;
                }
            }
        }
    }
    
    

    // Phương thức trả về các nhà cung cấp bị trùng lặp với id
    public function getDuplicates()
    {
        return $this->duplicates;
    }
}
