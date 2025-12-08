<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProductWarranties extends Model
{
    //
    use HasFactory;

    protected $table = 'product_warranties';

    protected $fillable = [
        'product_id',
        'info',
        'warranty',
    ];
    //function add product warranty
    public function addProductWarranty($data, $product_id)
    {
        if (!empty($data['info']) && !empty($data['warranty'])) {
            $warranties = array_values($data['warranty']); // Đảm bảo chỉ số đúng

            foreach ($data['info'] as $key => $info) {
                $warrantyValue = $warranties[$key] ?? 12;
                ProductWarranties::create([
                    'product_id' => $product_id,
                    'info' => $info,
                    'warranty' => (int) $warrantyValue,
                ]);
            }
        }
    }
    //function update product warranty
    public function updateProductWarranty($data, $product_id)
    {
        // Xóa bảo hành cũ
        ProductWarranties::where('product_id', $product_id)->delete();

        // Thêm bảo hành mới
        if (!empty($data['info']) && !empty($data['warranty'])) {
            foreach ($data['info'] as $key => $info) {
                ProductWarranties::create([
                    'product_id' => $product_id,
                    'info' => $info,
                    'warranty' => $data['warranty'][$key] ?? 12,
                ]);
            }
        }
    }
}
