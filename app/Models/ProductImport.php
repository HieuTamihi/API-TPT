<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImport extends Model
{
    use HasFactory;
    protected $table = 'product_import';
    protected $fillable = [
        'import_id',
        'product_id',
        'quantity',
        'sn_id',
        'note'
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function import()
    {
        return $this->belongsTo(Imports::class, 'import_id', 'id');
    }

    public function serialNumber()
    {
        return $this->belongsTo(SerialNumber::class, 'sn_id', 'id');
    }
}
