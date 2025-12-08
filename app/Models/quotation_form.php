<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class quotation_form extends Model
{
    use HasFactory;
    protected $table = 'quotation_form';
    protected $fillable = [
        'id',
        'content',
        'user_id',
    ];
}
