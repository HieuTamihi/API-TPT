<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationService extends Model
{
    protected $table = 'quotation_services';
    protected $fillable = [
        'quotation_id',
        'service_name',
        'unit',
        'brand',
        'quantity',
        'unit_price',
        'tax_rate',
        'total',
        'note',
    ];
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($quotationService) {
            // Thực hiện xóa khi có liên kết
            $quotationService->delete();
        });
    }
}
