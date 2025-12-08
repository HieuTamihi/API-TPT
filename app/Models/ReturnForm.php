<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnForm extends Model
{
    use HasFactory;
    protected $table = 'return_form';
    protected $fillable = [
        'reception_id',
        'return_code',
        'customer_id',
        'address',
        'date_created',
        'contact_person',
        'return_method',
        'user_id',
        'phone_number',
        'notes',
        'status',
    ];

    /**
     * Relationship with Reception.
     */
    public function reception()
    {
        return $this->belongsTo(Receiving::class, 'reception_id');
    }

    /**
     * Relationship with Customer.
     */
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    /**
     * Relationship with ProductReturn.
     */
    public function productReturns()
    {
        return $this->hasMany(ProductReturn::class, 'return_form_id');
    }
    public function getReturnFormAjax($data = null)
    {
        $returnForms = ReturnForm::with('customer', 'reception')
            ->select('return_form.*', 'customers.customer_name as customername', 'receiving.form_code_receiving as form_code_receiving', 'receiving.form_type', 'return_form.status as returnstatus')
            ->join('customers', 'return_form.customer_id', '=', 'customers.id')
            ->join('receiving', 'return_form.reception_id', '=', 'receiving.id');
        // Tìm kiếm chung
        if (!empty($data['search'])) {
            $returnForms->where(function ($query) use ($data) {
                $query->where('return_code', 'like', '%' . $data['search'] . '%')
                    ->orWhere('return_form.notes', 'like', '%' . $data['search'] . '%')
                    ->orWhereHas('productReturns.serialNumber', function ($subQuery) use ($data) {
                        $subQuery->where('serial_code', 'like', '%' . $data['search'] . '%');
                    })
                    ->orWhereHas('productReturns.replacementSerialNumber', function ($subQuery) use ($data) {
                        $subQuery->where('serial_code', 'like', '%' . $data['search'] . '%');
                    });
            });
        }
        // Lọc theo các trường cụ thể
        $filterableFields = [
            'ma' => 'return_code',
            'note' => 'return_form.notes',
        ];
        foreach ($filterableFields as $key => $field) {
            if (!empty($data[$key])) {
                $returnForms->where($field, 'like', '%' . $data[$key] . '%');
            }
        }
        
        // Lọc theo serial number
        if (!empty($data['serial'])) {
            $returnForms->where(function ($query) use ($data) {
                $query->whereHas('productReturns.serialNumber', function ($subQuery) use ($data) {
                    $subQuery->where('serial_code', 'like', '%' . $data['serial'] . '%');
                })
                ->orWhereHas('productReturns.replacementSerialNumber', function ($subQuery) use ($data) {
                    $subQuery->where('serial_code', 'like', '%' . $data['serial'] . '%');
                });
            });
        }
        if (!empty($data['customer'])) {
            $returnForms->whereHas('customer', function ($query) use ($data) {
                $query->whereIn('id', $data['customer']);
            });
        }
        if (!empty($data['receiving_code'])) {
            $returnForms->whereHas('reception', function ($query) use ($data) {
                $query->where('form_code_receiving', 'like', '%' . $data['receiving_code'] . '%');
            });
        }
        if (!empty($data['form_type'])) {
            $returnForms->whereHas('reception', function ($query) use ($data) {
                $query->whereIn('form_type', $data['form_type']);
            });
        }
        if (isset($data['status'])) {
            $returnForms = $returnForms->whereIn('return_form.status', $data['status']);
        }
        if (isset($data['date']) && is_array($data['date']) && count($data['date']) >= 2 && !empty($data['date'][0]) && !empty($data['date'][1])) {
            $dateStart = Carbon::parse($data['date'][0]);
            $dateEnd = Carbon::parse($data['date'][1])->endOfDay();
            $returnForms->whereBetween('return_form.date_created', [$dateStart, $dateEnd]);
        }
        if (isset($data['sort']) && isset($data['sort'][0])) {
            $returnForms = $returnForms->orderBy($data['sort'][0], $data['sort'][1]);
        }
        return $returnForms->get();
    }
}
