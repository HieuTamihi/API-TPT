<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use App\Models\Customers;

class CustomersImport implements ToCollection
{
    public $duplicates = [];

    public function collection(Collection $rows)
    {
        $rows = $rows->skip(1); // Bỏ qua hàng tiêu đề
    
        // Lấy danh sách tất cả mã số thuế có trong database
        $existingTaxCodes = Customers::pluck('id', 'tax_code')->toArray();
    
        foreach ($rows as $row) {
            $tax_code = $row[6] ?? null;
    
            if (!empty($tax_code) && isset($existingTaxCodes[$tax_code])) {
                // Nếu mã số thuế đã tồn tại, lưu vào danh sách trùng lặp
                $this->duplicates[] = [
                    'customer_id' => $existingTaxCodes[$tax_code], // Lấy ID khách hàng trùng
                    'row_data' => $row // Lưu dữ liệu hàng trùng lặp
                ];
            } else {
                // Nếu không trùng lặp, thêm vào database
                Customers::create([
                    'customer_code'  => $row[0] ?? null,
                    'customer_name'  => $row[1] ?? null,
                    'address'        => $row[2] ?? null,
                    'contact_person' => $row[3] ?? null,
                    'phone'          => $row[4] ?? null,
                    'email'          => $row[5] ?? null,
                    'tax_code'       => $tax_code,
                    'note'           => $row[7] ?? null,
                ]);
    
                // Cập nhật mảng kiểm tra trùng lặp
                if (!empty($tax_code)) {
                    $existingTaxCodes[$tax_code] = true;
                }
            }
        }
    }
    
    // Phương thức trả về các khách hàng bị trùng lặp với id
    public function getDuplicates()
    {
        return $this->duplicates;
    }
    
}

