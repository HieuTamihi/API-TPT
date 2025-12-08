<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SerialNumber extends Model
{
    use HasFactory;

    /**
     * Tên bảng liên kết với model.
     *
     * @var string
     */
    protected $table = 'serial_numbers';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'serial_code',
        'product_id',
        'status',
        'warehouse_id',
        'note'
    ];

    /**
     * Định nghĩa mối quan hệ với Product (nếu có bảng products).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function inventoryLookups()
    {
        return $this->hasMany(InventoryLookup::class, 'sn_id', 'id');
    }
    public function warrantyLookups()
    {
        return $this->hasMany(warrantyLookup::class, 'sn_id', 'id');
    }

    public function productImports()
    {
        return $this->hasMany(ProductImport::class, 'sn_id', 'id');
    }
    public function exports()
    {
        return $this->hasMany(ProductExport::class, 'sn_id', 'id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }
}
