<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Warehouse extends Model
{
    protected $fillable = [
        'type',
        'warehouse_code',
        'warehouse_name',
        'address',
    ];
    //relationship warehouseTransfer
    public function warehouseTransfer()
    {
        return $this->hasMany(WarehouseTransfer::class);
    }
    public function getAllWarehouse($data = null)
    {
        $warehouse = DB::table('warehouses');

        // Tìm kiếm chung
        if (!empty($data['search'])) {
            $warehouse->where(function ($query) use ($data) {
                $query->where('warehouse_code', 'like', '%' . $data['search'] . '%')
                    ->orWhere('warehouse_name', 'like', '%' . $data['search'] . '%')
                    ->orWhere('address', 'like', '%' . $data['search'] . '%');
            });
        }
        // Lọc theo các trường cụ thể
        $filterableFields = [
            'ma' => 'warehouse_code',
            'ten' => 'warehouse_name',
            'address' => 'address',
        ];
        foreach ($filterableFields as $key => $field) {
            if (!empty($data[$key])) {
                $warehouse->where($field, 'like', '%' . $data[$key] . '%');
            }
        }
        if (isset($data['sort']) && isset($data['sort'][0])) {
            $warehouse = $warehouse->orderBy($data['sort'][0], $data['sort'][1]);
        }
        return $warehouse->get();
    }
}
