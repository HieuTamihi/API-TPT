<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductExport extends Model
{
    //
    use HasFactory;
    protected $table = 'product_export';
    protected $fillable = [
        'export_id',
        'product_id',
        'quantity',
        'sn_id',
        'warranty',
        'note'
    ];
    public function export()
    {
        return $this->belongsTo(Exports::class, 'export_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function serialNumber()
    {
        return $this->belongsTo(SerialNumber::class, 'sn_id', 'id');
    }
}
