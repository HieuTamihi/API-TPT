<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $table = 'quotations';

    // Các trường có thể gán giá trị hàng loạt
    protected $fillable = [
        'reception_id',
        'quotation_code',
        'customer_id',
        'address',
        'quotation_date',
        'contact_person',
        'notes',
        'user_id',
        'contact_phone',
        'total_amount',
    ];

    // Nếu cần định nghĩa quan hệ
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function reception()
    {
        return $this->belongsTo(Receiving::class, 'reception_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function services()
    {
        return $this->hasMany(QuotationService::class, 'quotation_id');
    }
    public function getQuotationAjax($data = null)
    {
        $quotations = Quotation::with(['customer', 'reception'])
            ->select('quotations.*', 'customers.customer_name as customername', 'receiving.form_code_receiving as form_code_receiving', 'receiving.form_type')
            ->join('customers', 'quotations.customer_id', '=', 'customers.id')
            ->join('receiving', 'quotations.reception_id', '=', 'receiving.id');
        // Tìm kiếm chung
        if (!empty($data['search'])) {
            $quotations->where(function ($query) use ($data) {
                $query->where('quotation_code', 'like', '%' . $data['search'] . '%')
                    ->orWhere('quotations.notes', 'like', '%' . $data['search'] . '%');
            });
        }
        // Lọc theo các trường cụ thể
        $filterableFields = [
            'ma' => 'quotation_code',
            'note' => 'quotations.notes',
        ];
        foreach ($filterableFields as $key => $field) {
            if (!empty($data[$key])) {
                $quotations->where($field, 'like', '%' . $data[$key] . '%');
            }
        }
        if (!empty($data['customer'])) {
            $quotations->whereHas('customer', function ($query) use ($data) {
                $query->whereIn('id', $data['customer']);
            });
        }
        if (!empty($data['receiving_code'])) {
            $quotations->whereHas('reception', function ($query) use ($data) {
                $query->where('form_code_receiving', 'like', '%' . $data['receiving_code'] . '%');
            });
        }
        if (!empty($data['status'])) {
            $quotations->whereHas('reception', function ($query) use ($data) {
                $query->whereIn('form_type', $data['status']);
            });
        }
        if (!empty($data['date'][0]) && !empty($data['date'][1])) {
            $dateStart = Carbon::parse($data['date'][0]);
            $dateEnd = Carbon::parse($data['date'][1])->endOfDay();
            $quotations->whereBetween('quotation_date', [$dateStart, $dateEnd]);
        }
        if (isset($data['tong_tien'][0]) && isset($data['tong_tien'][1])) {
            $quotations = $quotations->where('total_amount', $data['tong_tien'][0], $data['tong_tien'][1]);
        }
        if (isset($data['sort']) && isset($data['sort'][0])) {
            $quotations = $quotations->orderBy($data['sort'][0], $data['sort'][1]);
        }

        return $quotations->get();
    }
}
