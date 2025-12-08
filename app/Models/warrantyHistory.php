<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyHistory extends Model
{
    protected $table = 'warranty_history';

    protected $fillable = [
        'warranty_lookup_id',
        'receiving_id',
        'return_id',
        'product_return_id',
        'note',
    ];

    // Relationship với bảng WarrantyLookup (nhiều WarrantyHistory thuộc về một WarrantyLookup)
    public function warrantyLookup()
    {
        return $this->belongsTo(WarrantyLookup::class, 'warranty_lookup_id');
    }

    // Relationship với bảng Receiving (nhiều WarrantyHistory thuộc về một Receiving)
    public function receiving()
    {
        return $this->belongsTo(Receiving::class, 'receiving_id');
    }

    // Relationship với bảng ReturnForm (nhiều WarrantyHistory thuộc về một ReturnForm)
    public function returnForm()
    {
        return $this->belongsTo(ReturnForm::class, 'return_id');
    }

    // Relationship với bảng ProductReturn (nhiều WarrantyHistory thuộc về một ProductReturn)
    public function productReturn()
    {
        return $this->belongsTo(ProductReturn::class, 'product_return_id');
    }
}
