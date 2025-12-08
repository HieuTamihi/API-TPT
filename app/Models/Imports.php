<?php

namespace App\Models;

use App\Helpers\GlobalHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Imports extends Model
{
    use HasFactory;
    protected $table = 'imports';

    protected $fillable = [
        'import_code',
        'user_id',
        'phone',
        'date_create',
        'provider_id',
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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function provider()
    {
        return $this->belongsTo(Providers::class, 'provider_id');
    }

    public function productImports()
    {
        return $this->hasMany(ProductImport::class, 'import_id', 'id');
    }

    public function getAllImports()
    {
        $warehouse_id = GlobalHelper::getWarehouseId();
        $imports = Imports::with(['warehouse:id,warehouse_name'])
            ->leftJoin("providers", "providers.id", "imports.provider_id")
            ->leftJoin("users", "users.id", "imports.user_id")
            ->select("providers.provider_name", "users.name", "imports.*")->orderBy('id', 'desc');
        if ($warehouse_id) {
            $imports = $imports->where('imports.warehouse_id', $warehouse_id);
        }
        // Paginate to avoid loading the entire dataset into memory
        return $imports->paginate(25);
    }

    public function addImport($data)
    {
        $warehouse_id = GlobalHelper::getWarehouseId();

        // Nếu không có import_code, tự động tạo mới
        $importCode = $data['import_code'] ?? self::generateImportCode();

        // Kiểm tra nếu mã nhập hàng đã tồn tại
        while (DB::table($this->table)->where('import_code', $importCode)->exists()) {
            $importCode = self::generateImportCode(); // Tạo mã mới
        }

        $arrImport = [
            'import_code' => $importCode,
            'user_id' => $data['user_id'],
            'phone' => $data['phone'],
            'date_create' => $data['date_create'],
            'provider_id' => $data['provider_id'],
            'contact_person' => $data['contact_person'],
            'address' => $data['address'],
            'note' => $data['note'],
            'warehouse_id' => $warehouse_id ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return DB::table($this->table)->insertGetId($arrImport);
    }


    public static function generateImportCode()
    {
        $prefix = 'PNH';

        // Lấy mã lớn nhất hiện tại theo prefix
        $lastCode = DB::table('imports')
            ->where('import_code', 'like', "{$prefix}%")
            ->orderBy('import_code', 'desc')
            ->value('import_code');

        // Tách số thứ tự nếu mã cuối cùng tồn tại
        $newNumber = 1; // Mặc định số thứ tự là 1
        if ($lastCode) {
            $lastNumber = (int) substr($lastCode, strlen($prefix)); // Lấy phần số sau prefix
            $newNumber = $lastNumber + 1;
        }

        // Định dạng số thứ tự thành chuỗi 5 chữ số (001, 002, ...)
        $formattedNumber = str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        // Kết hợp thành mã mới
        return "{$prefix}{$formattedNumber}";
    }
    
    public function getRecentImports(int $limit = 25)
    {
        $warehouse_id = GlobalHelper::getWarehouseId();
        $imports = Imports::with(['warehouse:id,warehouse_name'])
            ->leftJoin("providers", "providers.id", "imports.provider_id")
            ->leftJoin("users", "users.id", "imports.user_id")
            ->select("providers.provider_name", "users.name", "imports.*")
            ->orderBy('id', 'desc');
        if ($warehouse_id) {
            $imports = $imports->where('imports.warehouse_id', $warehouse_id);
        }
        return $imports->limit($limit)->get();
    }
    public function getImportAjax($data = null)
    {
        // Lấy dữ liệu Imports với quan hệ
        $imports = Imports::with(['user', 'provider'])

            ->join('users', 'imports.user_id', '=', 'users.id') // Join với bảng users
            ->join('providers', 'imports.provider_id', '=', 'providers.id')
            ->leftJoin('product_import', 'product_import.import_id', '=', 'imports.id')
            ->leftJoin('serial_numbers', 'serial_numbers.id', '=', 'product_import.sn_id')
            ->leftJoin('products', 'products.id', '=', 'product_import.product_id')
            ->select(
                'imports.*',
                'users.name as username',
                'providers.provider_name as provide_name',
                'serial_numbers.serial_code as serial_number',
                'products.product_name as product_name',
                'products.product_code as product_code'
            );
        if (!empty($data)) {
            if (!empty($data['search'])) {
                $imports->where(function ($query) use ($data) {
                    $query->where('import_code', 'like', '%' . $data['search'] . '%')
                        ->orWhere('imports.note', 'like', '%' . $data['search'] . '%')
                        ->orWhere('serial_numbers.serial_code', 'like', '%' . $data['search'] . '%')
                        ->orWhere('products.product_name', 'like', '%' . $data['search'] . '%')
                        ->orWhere('products.product_code', 'like', '%' . $data['search'] . '%');
                });
            }
            if (!empty($data['ma'])) {
                $imports->where('import_code', 'like', '%' . $data['ma'] . '%');
            }
            if (!empty($data['serial'])) {
                $imports->where('serial_numbers.serial_code', 'like', '%' . $data['serial'] . '%');
            }
            if (!empty($data['product_name'])) {
                $imports->where('products.product_name', 'like', '%' . $data['product_name'] . '%');
            }
            if (!empty($data['product_code'])) {
                $imports->where('products.product_code', 'like', '%' . $data['product_code'] . '%');
            }
            if (!empty($data['note'])) {
                $imports->where('imports.note', 'like', '%' . $data['note'] . '%');
            }
            if (!empty($data['date'][0]) && !empty($data['date'][1])) {
                $dateStart = Carbon::parse($data['date'][0]);
                $dateEnd = Carbon::parse($data['date'][1])->endOfDay();
                $imports->whereBetween('date_create', [$dateStart, $dateEnd]);
            }
            if (!empty($data['provider'])) {
                $imports->whereHas('provider', function ($query) use ($data) {
                    $query->whereIn('id', $data['provider']);
                });
            }
            if (!empty($data['user'])) {
                $imports->whereHas('user', function ($query) use ($data) {
                    $query->whereIn('id', $data['user']);
                });
            }
        }
        if (isset($data['sort']) && isset($data['sort'][0])) {
            $imports = $imports->orderBy($data['sort'][0], $data['sort'][1]);
        }
        // dd($imports->get());
        return $imports->get();
    }

    private function buildIndexQuery(array $data)
    {
        $warehouse_id = GlobalHelper::getWarehouseId();
        $imports = Imports::with(['warehouse:id,warehouse_name'])
            ->leftJoin('providers', 'providers.id', '=', 'imports.provider_id')
            ->leftJoin('users', 'users.id', '=', 'imports.user_id')
            ->select(
                'imports.*',
                'providers.provider_name as provider_name',
                'users.name as name'
            );

        if ($warehouse_id) {
            $imports->where('imports.warehouse_id', $warehouse_id);
        }

        // Text search across imports and existence in related tables without joining them
        if (!empty($data['search'])) {
            $search = $data['search'];
            $imports->where(function ($query) use ($search) {
                $query->where('imports.import_code', 'like', "%{$search}%")
                    ->orWhere('imports.note', 'like', "%{$search}%")
                    ->orWhereExists(function ($sub) use ($search) {
                        $sub->from('product_import')
                            ->leftJoin('serial_numbers', 'serial_numbers.id', '=', 'product_import.sn_id')
                            ->whereColumn('product_import.import_id', 'imports.id')
                            ->where('serial_numbers.serial_code', 'like', "%{$search}%");
                    })
                    ->orWhereExists(function ($sub) use ($search) {
                        $sub->from('product_import')
                            ->leftJoin('products', 'products.id', '=', 'product_import.product_id')
                            ->whereColumn('product_import.import_id', 'imports.id')
                            ->where('products.product_name', 'like', "%{$search}%");
                    })
                    ->orWhereExists(function ($sub) use ($search) {
                        $sub->from('product_import')
                            ->leftJoin('products', 'products.id', '=', 'product_import.product_id')
                            ->whereColumn('product_import.import_id', 'imports.id')
                            ->where('products.product_code', 'like', "%{$search}%");
                    });
            });
        }

        // Individual filters using EXISTS
        if (!empty($data['ma'])) {
            $imports->where('imports.import_code', 'like', "%{$data['ma']}%");
        }
        if (!empty($data['serial'])) {
            $serial = $data['serial'];
            $imports->whereExists(function ($sub) use ($serial) {
                $sub->from('product_import')
                    ->leftJoin('serial_numbers', 'serial_numbers.id', '=', 'product_import.sn_id')
                    ->whereColumn('product_import.import_id', 'imports.id')
                    ->where('serial_numbers.serial_code', 'like', "%{$serial}%");
            });
        }
        if (!empty($data['product_name'])) {
            $productName = $data['product_name'];
            $imports->whereExists(function ($sub) use ($productName) {
                $sub->from('product_import')
                    ->leftJoin('products', 'products.id', '=', 'product_import.product_id')
                    ->whereColumn('product_import.import_id', 'imports.id')
                    ->where('products.product_name', 'like', "%{$productName}%");
            });
        }
        if (!empty($data['product_code'])) {
            $productCode = $data['product_code'];
            $imports->whereExists(function ($sub) use ($productCode) {
                $sub->from('product_import')
                    ->leftJoin('products', 'products.id', '=', 'product_import.product_id')
                    ->whereColumn('product_import.import_id', 'imports.id')
                    ->where('products.product_code', 'like', "%{$productCode}%");
            });
        }
        if (!empty($data['note'])) {
            $imports->where('imports.note', 'like', "%{$data['note']}%");
        }
        if (!empty($data['date'][0]) && !empty($data['date'][1])) {
            $dateStart = Carbon::parse($data['date'][0]);
            $dateEnd = Carbon::parse($data['date'][1])->endOfDay();
            $imports->whereBetween('imports.date_create', [$dateStart, $dateEnd]);
        }
        if (!empty($data['provider']) && is_array($data['provider'])) {
            $imports->whereIn('imports.provider_id', $data['provider']);
        }
        if (!empty($data['user']) && is_array($data['user'])) {
            $imports->whereIn('imports.user_id', $data['user']);
        }

        // Sorting mapping
        if (!empty($data['sort'][0])) {
            $sortBy = $data['sort'][0];
            $sortDir = !empty($data['sort'][1]) ? $data['sort'][1] : 'DESC';
            $sortMap = [
                'import_code' => 'imports.import_code',
                'date_create' => 'imports.date_create',
                'provide_name' => 'providers.provider_name',
                'warehouse_id' => 'imports.warehouse_id',
                'username' => 'users.name',
                'note' => 'imports.note',
            ];
            $column = $sortMap[$sortBy] ?? 'imports.id';
            $imports->orderBy($column, $sortDir);
        } else {
            $imports->orderBy('imports.id', 'desc');
        }

        return $imports;
    }

    public function paginateForIndex(array $data, int $perPage = 50)
    {
        return $this->buildIndexQuery($data)->paginate($perPage);
    }

    public function getForExport(array $data)
    {
        return $this->buildIndexQuery($data)->get();
    }
}
