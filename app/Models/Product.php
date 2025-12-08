<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    // Đặt các cột có thể điền giá trị bằng phương thức create hoặc update
    protected $fillable = [
        'group_id',
        'product_code',
        'product_name',
        'brand',
        'warranty',
    ];

    /**
     * Thiết lập quan hệ nếu cần
     */

    public function serialNumbers()
    {
        return $this->hasMany(SerialNumber::class, 'product_id');
    }

    public function inventoryLookups()
    {
        return $this->hasMany(InventoryLookup::class, 'product_id', 'id');
    }

    public function productImports()
    {
        return $this->hasMany(ProductImport::class, 'product_id', 'id');
    }

    public function imports()
    {
        return $this->hasMany(ProductImport::class, 'product_id');
    }

    public function exports()
    {
        return $this->hasMany(ProductExport::class, 'product_id');
    }

    public function receivedProducts()
    {
        return $this->hasMany(ReceivedProduct::class, 'product_id');
    }

    public function returnProducts()
    {
        return $this->hasMany(ProductReturn::class, 'product_id');
    }

    // public function group()
    // {
    //     return $this->belongsTo(Group::class, 'group_id');
    // }

    //relation warwhouse_transfer_item
    public function warehouseTransferItems()
    {
        return $this->hasMany(WarehouseTransferItem::class, 'product_id');
    }

    public function getAllProducts($data = null)
    {
        $products = DB::table($this->table);
        // Tìm kiếm chung
        if (!empty($data['search'])) {
            $products->where(function ($query) use ($data) {
                $query->where('product_code', 'like', '%' . $data['search'] . '%')
                    ->orWhere('product_name', 'like', '%' . $data['search'] . '%')
                    ->orWhere('brand', 'like', '%' . $data['search'] . '%');
            });
        }
        // Lọc theo các trường cụ thể
        $filterableFields = [
            'ma' => 'product_code',
            'ten' => 'product_name',
            'hang' => 'brand',
        ];
        foreach ($filterableFields as $key => $field) {
            if (!empty($data[$key])) {
                $products->where($field, 'like', '%' . $data[$key] . '%');
            }
        }
        if (isset($data['bao_hanh'][0]) && isset($data['bao_hanh'][1])) {
            $products = $products->where('warranty', $data['bao_hanh'][0], $data['bao_hanh'][1]);
        }
        if (isset($data['sort']) && isset($data['sort'][0])) {
            $products = $products->orderBy($data['sort'][0], $data['sort'][1]);
        }

        return $products->get();
    }
}
