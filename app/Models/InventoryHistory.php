<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;
    protected $table = 'inventory_history';

    protected $fillable = [
        'inventory_lookup_id',
        'import_date',
        'storage_duration',
        'warranty_date',
        'note',
    ];
    public function inventoryLookup()
    {
        return $this->belongsTo(InventoryLookup::class, 'inventory_lookup_id');
    }
}
