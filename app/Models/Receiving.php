<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Receiving extends Model
{
    protected $table = 'receiving';

    protected $fillable = [
        'branch_id',
        'form_type',
        'form_code_receiving',
        'customer_id',
        'address',
        'date_created',
        'contact_person',
        'notes',
        'user_id',
        'phone',
        'closed_at',
        'status',
        'state',
    ];

    protected $casts = [
        'date_created' => 'datetime',
        'closed_at' => 'datetime',
    ];
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function receivedProducts()
    {
        return $this->hasMany(ReceivedProduct::class, 'reception_id');
    }
    public function getQuoteCount(string $prefix, $model, string $column)
    {
        // Lấy số thứ tự lớn nhất của mã phiếu hiện có
        $lastInvoiceNumber = $model::max($column);
        $lastNumber = 0;
        if ($lastInvoiceNumber) {
            preg_match("/{$prefix}(\d+)/", $lastInvoiceNumber, $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
        }
        $newInvoiceNumber = $lastNumber + 1;
        $countFormattedInvoice = str_pad($newInvoiceNumber, 6, '0', STR_PAD_LEFT);
        $invoiceNumber = "{$prefix}{$countFormattedInvoice}";

        return $invoiceNumber;
    }
    public function returnForms()
    {
        return $this->hasOne(ReturnForm::class, 'reception_id');
    }
    public function quotation()
    {
        return $this->hasOne(Quotation::class, 'reception_id');
    }
    public function getReceiAjax($data = null)
    {
        // Subqueries để tổng hợp serial và sản phẩm theo mỗi phiếu tiếp nhận
        $serialAggregate = DB::table('received_products')
            ->leftJoin('serial_numbers', 'serial_numbers.id', '=', 'received_products.serial_id')
            ->select(
                'received_products.reception_id',
                DB::raw('GROUP_CONCAT(DISTINCT serial_numbers.serial_code ORDER BY serial_numbers.serial_code SEPARATOR ", ") AS serial_number')
            )
            ->groupBy('received_products.reception_id');

        $productAggregate = DB::table('received_products')
            ->leftJoin('products', 'products.id', '=', 'received_products.product_id')
            ->select(
                'received_products.reception_id',
                DB::raw('GROUP_CONCAT(DISTINCT products.product_name ORDER BY products.product_name SEPARATOR ", ") AS product_name'),
                DB::raw('GROUP_CONCAT(DISTINCT products.product_code ORDER BY products.product_code SEPARATOR ", ") AS product_code')
            )
            ->groupBy('received_products.reception_id');

        $receivings = Receiving::with(['user', 'customer'])
            ->join('users', 'receiving.user_id', '=', 'users.id') // Join với bảng users
            ->join('customers', 'receiving.customer_id', '=', 'customers.id')
            ->leftJoinSub($serialAggregate, 'agg_serials', function ($join) {
                $join->on('agg_serials.reception_id', '=', 'receiving.id');
            })
            ->leftJoinSub($productAggregate, 'agg_products', function ($join) {
                $join->on('agg_products.reception_id', '=', 'receiving.id');
            })
            ->select(
                'receiving.*',
                'users.name as username',
                'customers.customer_name as customername',
                DB::raw('agg_serials.serial_number as serial_number'),
                DB::raw('agg_products.product_name as product_name'),
                DB::raw('agg_products.product_code as product_code')
            );
        if (!empty($data)) {
            if (!empty($data['search'])) {
                $receivings->where(function ($query) use ($data) {
                    $query->where('form_code_receiving', 'like', '%' . $data['search'] . '%')
                        ->orWhere('notes', 'like', '%' . $data['search'] . '%')
                        ->orWhereExists(function ($sub) use ($data) {
                            $sub->from('received_products')
                                ->leftJoin('serial_numbers', 'serial_numbers.id', '=', 'received_products.serial_id')
                                ->whereColumn('received_products.reception_id', 'receiving.id')
                                ->where('serial_numbers.serial_code', 'like', '%' . $data['search'] . '%');
                        })
                        ->orWhereExists(function ($sub) use ($data) {
                            $sub->from('received_products')
                                ->leftJoin('products', 'products.id', '=', 'received_products.product_id')
                                ->whereColumn('received_products.reception_id', 'receiving.id')
                                ->where(function ($q) use ($data) {
                                    $q->where('products.product_name', 'like', '%' . $data['search'] . '%')
                                      ->orWhere('products.product_code', 'like', '%' . $data['search'] . '%');
                                });
                        });
                });
            }
            if (!empty($data['serial'])) {
                $receivings->whereExists(function ($sub) use ($data) {
                    $sub->from('received_products')
                        ->leftJoin('serial_numbers', 'serial_numbers.id', '=', 'received_products.serial_id')
                        ->whereColumn('received_products.reception_id', 'receiving.id')
                        ->where('serial_numbers.serial_code', 'like', '%' . $data['serial'] . '%');
                });
            }
            if (!empty($data['product_name'])) {
                $receivings->whereExists(function ($sub) use ($data) {
                    $sub->from('received_products')
                        ->leftJoin('products', 'products.id', '=', 'received_products.product_id')
                        ->whereColumn('received_products.reception_id', 'receiving.id')
                        ->where('products.product_name', 'like', '%' . $data['product_name'] . '%');
                });
            }
            if (!empty($data['product_code'])) {
                $receivings->whereExists(function ($sub) use ($data) {
                    $sub->from('received_products')
                        ->leftJoin('products', 'products.id', '=', 'received_products.product_id')
                        ->whereColumn('received_products.reception_id', 'receiving.id')
                        ->where('products.product_code', 'like', '%' . $data['product_code'] . '%');
                });
            }
            if (!empty($data['ma'])) {
                $receivings->where('form_code_receiving', 'like', '%' . $data['ma'] . '%');
            }
            if (!empty($data['note'])) {
                $receivings->where('notes', 'like', '%' . $data['note'] . '%');
            }
            if (!empty($data['date'][0]) && !empty($data['date'][1])) {
                $dateStart = Carbon::parse($data['date'][0]);
                $dateEnd = Carbon::parse($data['date'][1])->endOfDay();
                $receivings->whereBetween('date_created', [$dateStart, $dateEnd]);
            }
            if (!empty($data['closed_at'][0]) && !empty($data['closed_at'][1])) {
                $dateStart = Carbon::parse($data['closed_at'][0]);
                $dateEnd = Carbon::parse($data['closed_at'][1])->endOfDay();
                $receivings->whereBetween('closed_at', [$dateStart, $dateEnd]);
            }
            if (!empty($data['customer'])) {
                $receivings->whereHas('customer', function ($query) use ($data) {
                    $query->whereIn('id', $data['customer']);
                });
            }
            if (isset($data['form_type'])) {
                $receivings = $receivings->whereIn('form_type', $data['form_type']);
            }
            if (isset($data['brand_type'])) {
                $receivings = $receivings->whereIn('branch_id', $data['brand_type']);
            }
            if (isset($data['status'])) {
                $receivings = $receivings->whereIn('receiving.status', $data['status']);
            }
            if (isset($data['state'])) {
                $receivings = $receivings->whereIn('state', $data['state']);
            }
            if (isset($data['sort']) && isset($data['sort'][0])) {
                $receivings = $receivings->orderBy($data['sort'][0], $data['sort'][1]);
            }
        }
        return $receivings->get();
    }
}
