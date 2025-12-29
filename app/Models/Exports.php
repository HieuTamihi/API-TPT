<?php

namespace App\Models;

use App\Helpers\GlobalHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Exports extends Model
{

    use HasFactory;
    protected $table = 'exports';

    protected $fillable = [
        'export_code',
        'user_id',
        'phone',
        'date_create',
        'customer_id',
        'contact_person',
        'address',
        'note',
        'warehouse_id',
    ];
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }
    public function productExport()
    {
        return $this->hasMany(ProductExport::class, 'export_id', 'id');
    }

    public static function generateExportCode()
    {
        $prefix = 'PXH';

        // Lấy mã lớn nhất hiện tại theo prefix
        $lastCode = DB::table('exports')
            ->where('export_code', 'like', "{$prefix}%")
            ->orderBy('export_code', 'desc')
            ->value('export_code');

        // Tách số thứ tự nếu mã cuối cùng tồn tại
        $newNumber = 1; // Mặc định số thứ tự là 1
        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, strlen($prefix)); // Lấy phần số sau prefix
            $newNumber  = $lastNumber + 1;
        }

        // Định dạng số thứ tự thành chuỗi 5 chữ số (001, 002, ...)
        $formattedNumber = str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        // Kết hợp thành mã mới
        return "{$prefix}{$formattedNumber}";
    }

    public function addExport($data)
    {
        $warehouse_id = GlobalHelper::getWarehouseId();

        // Nếu không có export_code, tự động tạo mới
        $exportCode = $data['export_code'] ?? self::generateExportCode();
        $originalCode = $exportCode;

        // Kiểm tra nếu mã xuất hàng đã tồn tại
        while (DB::table($this->table)->where('export_code', $exportCode)->exists()) {
            $exportCode = self::generateExportCode(); // Tạo mã mới
        }

        $arrExport = [
            'export_code'    => $exportCode,
            'user_id'        => $data['user_id'],
            'phone'          => $data['phone'],
            'date_create'    => $data['date_create'],
            'customer_id'    => $data['customer_id'],
            'address'        => $data['address'],
            'contact_person' => $data['contact_person'],
            'note'           => $data['note'],
            'warehouse_id'   => $warehouse_id ?? 1,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];

        return DB::table($this->table)->insertGetId($arrExport);
    }

    public function getExportAjax($data = null)
    {
        return $this->buildIndexQuery($data)->get();
    }

    private function buildIndexQuery(array $data)
    {
        $warehouse_id = GlobalHelper::getWarehouseId();
        $exports = Exports::with(['user:id,name', 'customer:id,customer_name'])
            ->leftJoin('users', 'users.id', '=', 'exports.user_id')
            ->leftJoin('customers', 'customers.id', '=', 'exports.customer_id')
            ->select('exports.*', 'users.name as username', 'customers.customer_name as customername');

        if ($warehouse_id) {
            $exports->where('exports.warehouse_id', $warehouse_id);
        }

        // TÌM KIẾM TỔNG QUÁT (Search bar trên mobile)
        if (!empty($data['search'])) {
            $search = $data['search'];
            $exports->where(function ($query) use ($search) {
                $query->where('exports.export_code', 'like', "%{$search}%")
                    ->orWhere('exports.note', 'like', "%{$search}%")
                    ->orWhere('customers.customer_name', 'like', "%{$search}%")
                    ->orWhereExists(function ($sub) use ($search) {
                        $sub->from('product_export')
                            ->leftJoin('serial_numbers', 'serial_numbers.id', '=', 'product_export.sn_id')
                            ->leftJoin('products', 'products.id', '=', 'product_export.product_id')
                            ->whereColumn('product_export.export_id', 'exports.id')
                            ->where('serial_numbers.serial_code', 'like', "%{$search}%")
                            ->orWhere('products.product_name', 'like', "%{$search}%")
                            ->orWhere('products.product_code', 'like', "%{$search}%");
                    });
            });
        }

        // LỌC CHUYÊN SÂU (Advanced Filter)
        if (!empty($data['ma'])) {
            $exports->where('exports.export_code', 'like', "%{$data['ma']}%");
        }
        if (!empty($data['serial'])) {
            $serial = $data['serial'];
            $exports->whereExists(function ($sub) use ($serial) {
                $sub->from('product_export')
                    ->join('serial_numbers', 'serial_numbers.id', '=', 'product_export.sn_id')
                    ->whereColumn('product_export.export_id', 'exports.id')
                    ->where('serial_numbers.serial_code', 'like', "%{$serial}%");
            });
        }
        if (!empty($data['date']) && is_array($data['date'])) {
            $start = Carbon::parse($data['date'][0])->startOfDay();
            $end = Carbon::parse($data['date'][1] ?? $data['date'][0])->endOfDay();
            $exports->whereBetween('exports.date_create', [$start, $end]);
        }

        // Sắp xếp
        $sortBy = $data['sort_by'] ?? 'id';
        $sortDir = $data['sort_dir'] ?? 'desc';
        $exports->orderBy("exports.$sortBy", $sortDir);

        return $exports;
    }

    public function paginateForIndex(array $data, int $perPage = 25)
    {
        return $this->buildIndexQuery($data)->paginate($perPage);
    }

    public function getForExport(array $data)
    {
        return $this->buildIndexQuery($data)->get();
    }

    public function getRecentExports(int $limit = 50)
    {
        $warehouse_id = GlobalHelper::getWarehouseId();
        $exports = Exports::with(['user', 'customer'])
            ->leftJoin('users', 'users.id', '=', 'exports.user_id')
            ->leftJoin('customers', 'customers.id', '=', 'exports.customer_id')
            ->select(
                'exports.*',
                'users.name as username',
                'customers.customer_name as customername'
            )
            ->orderBy('exports.id', 'desc');
        if ($warehouse_id) {
            $exports->where('exports.warehouse_id', $warehouse_id);
        }
        return $exports->limit($limit)->get();
    }
}
