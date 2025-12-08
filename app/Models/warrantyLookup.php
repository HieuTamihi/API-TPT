<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class warrantyLookup extends Model
{
    protected $table = 'warranty_lookup';

    protected $fillable = [
        'product_id',
        'sn_id',
        'customer_id',
        'export_return_date',
        'warranty',
        'name_warranty',
        'status',
        'name_status',
        'warranty_expire_date',
        'warranty_extra',
        'name_expire_date',
        'return_date',
        'service_warranty_expired',
        'export_id',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function serialNumber()
    {
        return $this->belongsTo(SerialNumber::class, 'sn_id', 'id');
    }
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }
    public function warrantyHistories()
    {
        return $this->hasMany(warrantyHistory::class, 'warranty_lookup_id');
    }
    public function getWarranAjax($data = null)
    {
        $warrantyLookup = warrantyLookup::join('products', 'products.id', '=', 'warranty_lookup.product_id')
            ->join('serial_numbers', 'serial_numbers.id', '=', 'warranty_lookup.sn_id')
            ->join('customers', 'customers.id', '=', 'warranty_lookup.customer_id')
            ->with('product', 'serialNumber', 'customer')
            ->select(
                'warranty_lookup.*',
                'serial_numbers.serial_code as sericode',
                'serial_numbers.id as serial_id',
                'products.*',
                'customers.customer_name as customername',
                'warranty_lookup.status as status',
                'warranty_lookup.id as id',
                'warranty_lookup.warranty as warrantyLookup',
            );
    
        if (!empty($data['search'])) {
            $warrantyLookup->where(function ($query) use ($data) {
                $query->where('products.product_code', 'like', '%' . $data['search'] . '%')
                    ->orWhere('products.product_name', 'like', '%' . $data['search'] . '%')
                    ->orWhere('products.brand', 'like', '%' . $data['search'] . '%')
                    ->orWhere('customers.customer_name', 'like', '%' . $data['search'] . '%')
                    ->orWhere('serial_numbers.serial_code', 'like', '%' . $data['search'] . '%')
                    ->orWhere('warranty_lookup.name_warranty', 'like', '%' . $data['search'] . '%')
                    ->orWhere('warranty_lookup.name_expire_date', 'like', '%' . $data['search'] . '%');
            });
        }
    
        $filterableFields = [
            'ma' => 'products.product_code',
            'ten' => 'products.product_name',
            'brand' => 'products.brand',
            'sn' => 'serial_numbers.serial_code',
        ];
    
        foreach ($filterableFields as $key => $field) {
            if (!empty($data[$key])) {
                $warrantyLookup->where($field, 'like', '%' . $data[$key] . '%');
            }
        }
    
        if (!empty($data['customer'])) {
            $warrantyLookup->whereHas('customer', function ($query) use ($data) {
                $query->whereIn('id', $data['customer']);
            });
        }
    
        if (isset($data['status'])) {
            $warrantyLookup->whereIn('warranty_lookup.status', $data['status']);
        }
    
        if (!empty($data['date'][0]) && !empty($data['date'][1])) {
            $dateStart = Carbon::parse($data['date'][0]);
            $dateEnd = Carbon::parse($data['date'][1])->endOfDay();
            $warrantyLookup->whereBetween('export_return_date', [$dateStart, $dateEnd]);
        }
        if (!empty($data['date_expired'][0]) && !empty($data['date_expired'][1])) {
            $dateStart = Carbon::parse($data['date_expired'][0]);
            $dateEnd = Carbon::parse($data['date_expired'][1])->endOfDay();
            $warrantyLookup->whereBetween('return_date', [$dateStart, $dateEnd]);
        }
    
        if (isset($data['bao_hanh'][0]) && isset($data['bao_hanh'][1])) {
            $warrantyLookup->whereBetween('warranty_lookup.warranty', [$data['bao_hanh'][0], $data['bao_hanh'][1]]);
        }
        if (isset($data['bao_hanh_dich_vu'][0]) && isset($data['bao_hanh_dich_vu'][1])) {
            $warrantyLookup->whereBetween('warranty_lookup.warranty_extra', [$data['bao_hanh_dich_vu'][0], $data['bao_hanh_dich_vu'][1]]);
        }
    
        if (isset($data['sort']) && isset($data['sort'][0])) {
            $warrantyLookup->orderBy($data['sort'][0], $data['sort'][1]);
        } else {
            // Sắp xếp mặc định theo ID mới nhất
            $warrantyLookup->orderBy('warranty_lookup.id', 'desc');
        }
        
        // Sử dụng pagination trực tiếp từ database thay vì group sau
        $perPage = 25;
        $currentPage = isset($data['page']) ? $data['page'] : 1;
        
        $warranties = $warrantyLookup->paginate($perPage, ['*'], 'page', $currentPage);
        
        // Nhóm dữ liệu theo `sn_id` cho trang hiện tại
        $grouped = $warranties->groupBy('sn_id')->map(function ($items) {
            // Sao chép dữ liệu để tránh ảnh hưởng đến bản gốc
            $first = $items->first()->replicate();
            $first->id = $items->first()->sn_id;
            // Gộp name_warranty và warranty thành chuỗi
            $first->name_warranty = $items->map(function ($item) {
                $warrantyValue = $item->warrantyLookup ?: $item->warranty;
                return $item->name_warranty . ": " . $warrantyValue . " tháng";
            })->join('; ');
    
            // Gộp status thành chuỗi theo định dạng yêu cầu
            $first->status_string = $items->map(function ($item) {
                $statusText = $item->status == 1 ? 'Hết bảo hành' : 'Còn bảo hành';
                return $item->name_warranty . ": " . $statusText;
            })->join('| ');
    
            return $first;
        });
        
        // Tạo paginator mới với grouped data
        $grouped = $grouped->values();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $grouped,
            $warranties->total(), // Sử dụng total từ pagination gốc
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    
        return $paginated;
    }    
}
