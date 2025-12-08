<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_returns';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'return_form_id',
        'product_id',
        'quantity',
        'serial_number_id',
        'replacement_code',
        'replacement_serial_number_id',
        'extra_warranty',
        'notes',
    ];

    /**
     * Define the relationship with the ReturnForm model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function returnForm()
    {
        return $this->belongsTo(ReturnForm::class, 'return_form_id');
    }

    /**
     * Define the relationship with the Product model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function product_replace()
    {
        return $this->belongsTo(Product::class, 'replacement_code');
    }

    /**
     * Define the relationship with the SerialNumber model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serialNumber()
    {
        return $this->belongsTo(SerialNumber::class, 'serial_number_id');
    }

    /**
     * Define the relationship with the replacement serial number.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function replacementSerialNumber()
    {
        return $this->belongsTo(SerialNumber::class, 'replacement_serial_number_id');
    }
    public function warranties()
    {
        return $this->hasOne(WarrantyReceived::class,  'product_return_id', 'id');
    }
    public static function createProductReturn(array $data)
    {
        return self::create([
            'return_form_id' => $data['return_form_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'serial_number_id' => $data['serial_number_id'],
            'replacement_code' => $data['replacement_code'] ?? null,
            'replacement_serial_number_id' => $data['replacement_serial_number_id'] ?? null,
            'extra_warranty' => $data['extra_warranty'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }
}
