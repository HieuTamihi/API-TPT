<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\InventoryHistory;
use App\Models\InventoryLookup;
use App\Models\Product;
use App\Models\Providers;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryLookupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $inventoryLookup;
    public function __construct(InventoryLookup $inventoryLookup)
    {
        $this->inventoryLookup = $inventoryLookup;
    }
    public function index()
    {
        $title = "Tra cứu tồn kho";
        $warehouse_id = GlobalHelper::getWarehouseId();

        // Lấy danh sách serial (sn_id != 0)
        $withSerial = InventoryLookup::with(['product', 'serialNumber', 'provider'])
            ->whereHas('serialNumber', function ($q) {
                $q->whereIn('status', [1, 5]);
            });

        // Lấy danh sách không serial (sn_id = 0) - sử dụng raw query để tránh lỗi GROUP BY
        $noSerial = DB::table('inventory_lookup')
            ->join('products', 'products.id', '=', 'inventory_lookup.product_id')
            ->join('providers', 'providers.id', '=', 'inventory_lookup.provider_id')
            ->where('inventory_lookup.sn_id', 0)
            ->select(
                'inventory_lookup.id',
                'inventory_lookup.product_id',
                'inventory_lookup.sn_id',
                'inventory_lookup.provider_id',
                'inventory_lookup.import_date',
                'inventory_lookup.storage_duration',
                'inventory_lookup.status',
                'inventory_lookup.warranty_date',
                'inventory_lookup.note',
                'inventory_lookup.warehouse_id',
                'inventory_lookup.import_id',
                DB::raw('SUM(inventory_lookup.remaining_quantity) as remaining_quantity')
            )
            ->groupBy(
                'inventory_lookup.id',
                'inventory_lookup.product_id',
                'inventory_lookup.sn_id',
                'inventory_lookup.provider_id',
                'inventory_lookup.import_date',
                'inventory_lookup.storage_duration',
                'inventory_lookup.status',
                'inventory_lookup.warranty_date',
                'inventory_lookup.note',
                'inventory_lookup.warehouse_id',
                'inventory_lookup.import_id'
            );

        // Áp điều kiện kho nếu cần
        if (Auth::user()->roles()->first()->id != 1 && !Auth::user()->hasAnyRole(['Quản lý kho'])) {
            if ($warehouse_id) {
                $withSerial = $withSerial->whereHas('serialNumber', function ($q) use ($warehouse_id) {
                    $q->where('warehouse_id', $warehouse_id);
                });

                $noSerial = $noSerial->where('inventory_lookup.warehouse_id', $warehouse_id);
            }
        }

        // Lấy kết quả
        $withSerial = $withSerial->get();
        $noSerial = $noSerial->get();

        // Chuyển đổi noSerial thành collection của model
        $noSerialModels = collect($noSerial)->map(function ($item) {
            $model = new InventoryLookup();
            $model->id = $item->id;
            $model->product_id = $item->product_id;
            $model->sn_id = $item->sn_id;
            $model->provider_id = $item->provider_id;
            $model->import_date = $item->import_date;
            $model->storage_duration = $item->storage_duration;
            $model->status = $item->status;
            $model->warranty_date = $item->warranty_date;
            $model->note = $item->note;
            $model->warehouse_id = $item->warehouse_id;
            $model->import_id = $item->import_id;
            $model->remaining_quantity = $item->remaining_quantity;

            // Load relationships
            $model->setRelation('product', Product::find($item->product_id));
            $model->setRelation('provider', Providers::find($item->provider_id));

            return $model;
        });

        // Gộp lại và sắp xếp theo ID mới nhất
        $allInventory = $withSerial->concat($noSerialModels)->sortByDesc('id')->values();

        // Tạo pagination tùy chỉnh cho 25 items per page
        $perPage = 25;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $allInventory->slice($offset, $perPage)->values();

        // Tạo paginator tùy chỉnh
        $inventory = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allInventory->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        $providers = Providers::all();
        return view('expertise.inventoryLookup.index', compact('title', 'inventory', 'providers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryLookup $inventoryLookup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = "Tra cứu tồn kho";
        $inventoryLookup = InventoryLookup::with(['product', 'serialNumber'])
            ->where("id", $id)
            ->first();
        if ($inventoryLookup) {
            $histories = InventoryHistory::with('inventoryLookup')
                ->where("inventory_lookup_id", $id)
                ->orderBy('created_at', 'desc')
                ->get();
            return view('expertise.inventoryLookup.edit', compact('title', 'inventoryLookup', 'histories'));
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id, Request $request)
    {
        $inventoryLookup = InventoryLookup::findOrFail($id);
        $inventoryLookup->warranty_date = $request->warranty_date;
        $inventoryLookup->note = $request->note;
        $inventoryLookup->status = 0;
        $inventoryLookup->save();
        //Lưu lịch sử 
        if (!empty($request->warranty_date)) {
            // Thêm mới vào inventory_history
            InventoryHistory::create([
                'inventory_lookup_id' => $id,
                'import_date' => $inventoryLookup->import_date,
                'storage_duration' => $inventoryLookup->storage_duration,
                'warranty_date' => $request->warranty_date,
                'note' => $request->note,
            ]);
        }
        return redirect()->route('inventoryLookup.index')->with('msg', 'Cập nhật thành công bảo trì định kỳ!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryLookup $inventoryLookup)
    {
        //
    }
    public function filterData(Request $request)
    {
        $data = $request->all();
        $filters = [];
        if (isset($data['ma']) && $data['ma'] !== null) {
            $filters[] = ['value' => 'Mã hàng: ' . $data['ma'], 'name' => 'ma-hang', 'icon' => 'po'];
        }
        if (isset($data['ten']) && $data['ten'] !== null) {
            $filters[] = ['value' => 'Tên hàng: ' . $data['ten'], 'name' => 'ten-hang', 'icon' => 'product'];
        }
        if (isset($data['brand']) && $data['brand'] !== null) {
            $filters[] = ['value' => 'Hãng: ' . $data['brand'], 'name' => 'hang', 'icon' => 'brand'];
        }
        if (isset($data['sn']) && $data['sn'] !== null) {
            $filters[] = ['value' => 'Serial: ' . $data['sn'], 'name' => 'serial', 'icon' => 'sn'];
        }
        if (isset($data['provider']) && $data['provider'] !== null) {
            $filters[] = ['value' => 'Nhà cung cấp: ' . count($data['provider']) . ' đã chọn', 'name' => 'nha-cung-cap', 'icon' => 'provider'];
        }
        if (isset($data['date']) && $data['date'][1] !== null) {
            $date_start = date("d/m/Y", strtotime($data['date'][0]));
            $date_end = date("d/m/Y", strtotime($data['date'][1]));
            $filters[] = ['value' => 'Ngày nhập hàng: từ ' . $date_start . ' đến ' . $date_end, 'name' => 'ngay-nhap-hang', 'icon' => 'date'];
        }
        if (isset($data['status']) && $data['status'] !== null) {
            $statusValues = [];
            if (in_array(1, $data['status'])) {
                $statusValues[] = '<span style="color: #858585;">Tới hạn bảo trì</span>';
            }
            if (in_array(0, $data['status'])) {
                $statusValues[] = '<span style="color: #08AA36BF;">Blank</span>';
            }
            $filters[] = ['value' => 'Tình trạng: ' . implode(', ', $statusValues), 'name' => 'trang-thai', 'icon' => 'status'];
        }
        if (isset($data['time_inven']) && $data['time_inven'][1] !== null) {
            $filters[] = ['value' => 'Thời gian tồn kho: ' . $data['time_inven'][0] . ' ' . $data['time_inven'][1], 'name' => 'thoi-gian-ton-kho', 'icon' => 'money'];
        }

        if ($request->ajax()) {
            $inventoryLookup = $this->inventoryLookup->getInvenAjax($data);
            return response()->json([
                'data' => $inventoryLookup->items(),
                'pagination' => [
                    'current_page' => $inventoryLookup->currentPage(),
                    'last_page' => $inventoryLookup->lastPage(),
                    'per_page' => $inventoryLookup->perPage(),
                    'total' => $inventoryLookup->total(),
                    'from' => $inventoryLookup->firstItem(),
                    'to' => $inventoryLookup->lastItem(),
                ],
                'filters' => $filters,
            ]);
        }
        return false;
    }
    public function summary(Request $request)
    {
        $warehouse_id = GlobalHelper::getWarehouseId();

        $inventory = InventoryLookup::with(['product', 'serialNumber'])
            ->whereHas('serialNumber', function ($query) {
                $query->whereIn('status', [1, 5]);
            });

        if (Auth::user()->roles()->first()->id != 1 && !Auth::user()->hasAnyRole(['Quản lý kho'])) {
            if ($warehouse_id) {
                $inventory = $inventory->whereHas('serialNumber', function ($query) use ($warehouse_id) {
                    $query->where('warehouse_id', $warehouse_id);
                });
            }
        }

        // Chỉ lọc trên 3 cột: product_code, product_name, brand
        if ($request->filled('ma')) {
            $inventory->whereHas('product', function ($query) use ($request) {
                $query->where('product_code', 'like', '%' . $request->input('ma') . '%');
            });
        }

        if ($request->filled('ten')) {
            $inventory->whereHas('product', function ($query) use ($request) {
                $query->where('product_name', 'like', '%' . $request->input('ten') . '%');
            });
        }

        if ($request->filled('brand')) {
            $inventory->whereHas('product', function ($query) use ($request) {
                $query->where('brand', 'like', '%' . $request->input('brand') . '%');
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $inventory->whereHas('product', function ($query) use ($search) {
                $query->where('product_code', 'like', '%' . $search . '%')
                    ->orWhere('product_name', 'like', '%' . $search . '%')
                    ->orWhere('brand', 'like', '%' . $search . '%');
            });
        }

        // Tính toán tổng hợp
        $summary = $inventory
            ->selectRaw('products.product_code, products.product_name, products.brand, SUM(inventory_lookup.remaining_quantity) as quantity')
            ->join('products', 'inventory_lookup.product_id', '=', 'products.id')
            ->groupBy('products.product_code', 'products.product_name', 'products.brand')
            ->get();

        // --- Hàng không có serial ---
        $summaryWithoutSerial = InventoryLookup::query()
            ->join('products', 'inventory_lookup.product_id', '=', 'products.id')
            ->where('sn_id', 0)
            ->where('remaining_quantity', '>', 0)
            ->when($request->filled('ma'), fn($q) => $q->where('products.product_code', 'like', '%' . $request->ma . '%'))
            ->when($request->filled('ten'), fn($q) => $q->where('products.product_name', 'like', '%' . $request->ten . '%'))
            ->when($request->filled('brand'), fn($q) => $q->where('products.brand', 'like', '%' . $request->brand . '%'))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($sub) use ($search) {
                    $sub->where('products.product_code', 'like', "%$search%")
                        ->orWhere('products.product_name', 'like', "%$search%")
                        ->orWhere('products.brand', 'like', "%$search%");
                });
            })
            ->selectRaw('products.product_code, products.product_name, products.brand, SUM(inventory_lookup.remaining_quantity) as quantity')
            ->groupBy('products.product_code', 'products.product_name', 'products.brand')
            ->get();

        // Gộp lại
        $summary = $summary->concat($summaryWithoutSerial)
            ->groupBy(fn($item) => $item->product_code . '|' . $item->product_name . '|' . $item->brand)
            ->map(function ($group) {
                $first = $group->first();
                $first->quantity = $group->sum('quantity');
                return $first;
            })
            ->values();

        return response()->json(['data' => $summary]);
    }
    //     public function summary()
    // {
    //     $columns = \DB::getSchemaBuilder()->getColumnListing('products');
    //     dd($columns); // Xem danh sách cột của bảng products
    // }

    /**
     * API Check tồn kho nhanh
     */
    public function checkStock(Request $request)
    {
        // 1. Validate
        $request->validate([
            'keyword' => 'required|string|min:2' // Yêu cầu nhập ít nhất 2 ký tự
        ]);

        $keyword = trim($request->keyword);

        // 2. Query tìm kiếm
        // Tìm trong bảng tồn kho, khớp với Sản phẩm hoặc Serial
        $inventoryItems = InventoryLookup::query()
            ->with([
                'product:id,product_name,product_code,brand,warranty', // Lấy thông tin SP
                'warehouse:id,warehouse_name',          // Lấy tên kho
                'serialNumber:id,serial_code' // Lấy mã serial (nếu tìm theo serial)
            ])
            ->where(function ($query) use ($keyword) {
                // a. Tìm theo Product Code hoặc Product Name
                $query->whereHas('product', function ($q) use ($keyword) {
                    $q->where('product_code', 'like', "%{$keyword}%")
                        ->orWhere('product_name', 'like', "%{$keyword}%");
                })
                    // b. Tìm theo Serial Code
                    ->orWhereHas('serialNumber', function ($q) use ($keyword) {
                        $q->where('serial_code', $keyword); // Serial thường tìm chính xác
                    });
            })
            ->get();

        // 3. Nếu không có kết quả
        if ($inventoryItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy sản phẩm nào còn hàng với từ khóa: '$keyword'",
                'total_stock' => 0,
                'data' => []
            ]);
        }

        // 4. Xử lý dữ liệu trả về (Grouping)
        // Gom nhóm theo Sản phẩm (vì 1 từ khóa có thể ra nhiều sản phẩm khác nhau)
        $groupedByProduct = $inventoryItems->groupBy('product_id');

        $result = $groupedByProduct->map(function ($items) {
            $productInfo = $items->first()->product;

            // Tính tổng tồn kho của sản phẩm này
            $totalQty = $items->sum('remaining_quantity');

            // Chi tiết tồn kho theo từng kho (Group by Warehouse)
            $warehouseDetails = $items->groupBy('warehouse_id')->map(function ($whItems) {
                $whInfo = $whItems->first()->warehouse;
                return [
                    'warehouse_name' => $whInfo->warehouse_name ?? 'Kho chưa đặt tên',
                    'quantity'       => $whItems->sum('remaining_quantity'),
                    // Nếu cần hiển thị list Serial có trong kho này thì bỏ comment dòng dưới:
                    // 'serials'     => $whItems->pluck('serialNumber.serial_code')->filter()->values()
                ];
            })->values();

            return [
                'product_name' => $productInfo->product_name ?? 'N/A',
                'product_code' => $productInfo->product_code ?? 'N/A',
                'brand'        => $productInfo->brand ?? 'N/A',
                'total_stock'  => $totalQty,
                'locations'    => $warehouseDetails
            ];
        })->values();

        return response()->json([
            'success' => true,
            'search_keyword' => $keyword,
            'total_products_found' => $result->count(),
            'data' => $result
        ]);
    }

    /**
     * Danh sách tồn kho + tìm kiếm + lọc + sắp xếp + phân trang
     * Tương tự method getInvenAjax trong model
     */
    public function list(Request $request): JsonResponse
    {
        $inputData = [];

        // Lấy tham số request ...
        // (Giữ nguyên phần xử lý input của bạn ở trên: provider, status, dates...)
        if ($request->filled('search')) $inputData['search'] = $request->search;
        if ($request->filled('ma')) $inputData['ma'] = $request->ma;
        if ($request->filled('ten')) $inputData['ten'] = $request->ten;
        if ($request->filled('brand')) $inputData['brand'] = $request->brand;
        if ($request->filled('sn')) $inputData['sn'] = $request->sn;
        if ($request->filled('provider')) $inputData['provider'] = (array)$request->provider; // Fix lỗi count()
        if ($request->filled('status')) $inputData['status'] = (array)$request->status; // Fix lỗi count()
        if ($request->filled('date_from')) $inputData['date'] = [$request->date_from, $request->date_to];
        if ($request->filled('duration_min')) $inputData['time_inven'] = [$request->duration_min, $request->duration_max];

        // Sắp xếp
        if ($request->has('sort_by') && $request->has('sort_dir')) {
            $sortMap = [
                'code' => 'products.product_code',
                'name' => 'products.product_name',
                'brand' => 'products.brand',
                'serial' => 'serial_numbers.serial_code',
                'provider' => 'providers.provider_name',
                'import_date' => 'inventory_lookup.import_date',
                'warehouse' => 'warehouses.warehouse_name',
                'duration' => 'inventory_lookup.storage_duration',
            ];
            $field = $sortMap[$request->sort_by] ?? 'inventory_lookup.id';
            $inputData['sort'] = [$field, $request->sort_dir];
        }

        // --- BẮT ĐẦU SỬA QUERY TẠI ĐÂY ---

        $query = InventoryLookup::select(
            'inventory_lookup.*',
            'products.product_code',
            'products.product_name',
            'products.brand',
            'providers.provider_name',
            'warehouses.warehouse_name',
            // Sử dụng leftJoin nên cần xử lý null cho serial
            DB::raw('IFNULL(serial_numbers.serial_code, "") as serial_code'),
            DB::raw('IFNULL(serial_numbers.status, 0) as serial_status')
        )
            ->join('products', 'products.id', '=', 'inventory_lookup.product_id')
            ->join('providers', 'providers.id', '=', 'inventory_lookup.provider_id')
            ->leftJoin('warehouses', 'warehouses.id', '=', 'inventory_lookup.warehouse_id')
            // QUAN TRỌNG: Đổi thành LEFT JOIN để lấy cả hàng không có serial (sn_id=0)
            ->leftJoin('serial_numbers', 'serial_numbers.id', '=', 'inventory_lookup.sn_id');

        // QUAN TRỌNG: Điều kiện lọc (Tương tự logic trong hàm index của Web)
        $query->where(function ($q) {
            // Trường hợp 1: Có Serial (sn_id > 0) -> Phải check status trong bảng serial
            $q->where(function ($sub) {
                $sub->where('inventory_lookup.sn_id', '>', 0)
                    ->whereIn('serial_numbers.status', [1, 5]); // 1: Trong kho, 5: Đang mượn
            })
                // Trường hợp 2: Không có Serial (sn_id = 0) -> Check số lượng tồn > 0
                ->orWhere(function ($sub) {
                    $sub->where('inventory_lookup.sn_id', 0)
                        ->where('inventory_lookup.remaining_quantity', '>', 0);
                });
        });

        // === PHÂN QUYỀN THEO KHO ===
        $user = Auth::user();
        if ($user) {
            $isAdminOrManager = $user->hasAnyRole(['Quản lý kho']) || ($user->roles && $user->roles->id == 1);

            if (!$isAdminOrManager) {
                $warehouseId = \App\Helpers\GlobalHelper::getWarehouseId();
                if ($warehouseId) {
                    // Logic phân quyền: Nếu có serial check theo serial, không thì check theo inventory
                    $query->where(function ($q) use ($warehouseId) {
                        $q->where('serial_numbers.warehouse_id', $warehouseId)
                            ->orWhere('inventory_lookup.warehouse_id', $warehouseId);
                    });
                }
            }
        }

        // --- CÁC BỘ LỌC TÌM KIẾM ---

        // 1. Tìm kiếm chung
        if (!empty($inputData['search'])) {
            $search = $inputData['search'];
            $query->where(function ($q) use ($search) {
                $q->where('products.product_code', 'like', "%{$search}%")
                    ->orWhere('products.product_name', 'like', "%{$search}%")
                    ->orWhere('products.brand', 'like', "%{$search}%")
                    ->orWhere('providers.provider_name', 'like', "%{$search}%")
                    ->orWhere('serial_numbers.serial_code', 'like', "%{$search}%");
            });
        }

        // 2. Lọc chi tiết
        if (!empty($inputData['ma'])) $query->where('products.product_code', 'like', "%{$inputData['ma']}%");
        if (!empty($inputData['ten'])) $query->where('products.product_name', 'like', "%{$inputData['ten']}%");
        if (!empty($inputData['brand'])) $query->where('products.brand', 'like', "%{$inputData['brand']}%");
        if (!empty($inputData['sn'])) $query->where('serial_numbers.serial_code', 'like', "%{$inputData['sn']}%");

        // 3. Lọc nhà cung cấp
        if (!empty($inputData['provider'])) {
            $query->whereIn('inventory_lookup.provider_id', $inputData['provider']);
        }

        // 4. Lọc trạng thái (Chỉ áp dụng chính xác cho hàng có serial)
        if (!empty($inputData['status'])) {
            // Nếu lọc trạng thái, ta ưu tiên check bảng serial. 
            // Nếu hàng không serial, mặc định coi là trạng thái '0' (hoặc logic riêng của bạn)
            $query->where(function ($q) use ($inputData) {
                $q->whereIn('serial_numbers.status', $inputData['status'])
                    ->orWhereIn('inventory_lookup.status', $inputData['status']);
            });
        }

        // 5. Lọc ngày và thời gian
        if (!empty($inputData['date'][0])) {
            $start = Carbon::parse($inputData['date'][0])->startOfDay();
            $end = Carbon::parse($inputData['date'][1])->endOfDay();
            $query->whereBetween('inventory_lookup.import_date', [$start, $end]);
        }
        if (isset($inputData['time_inven'])) {
            $query->whereBetween('inventory_lookup.storage_duration', $inputData['time_inven']);
        }

        // Sắp xếp
        if (isset($inputData['sort'])) {
            $query->orderBy($inputData['sort'][0], $inputData['sort'][1]);
        } else {
            $query->orderBy('inventory_lookup.id', 'desc');
        }

        // Phân trang
        $perPage = $request->get('per_page', 25);
        $inventory = $query->paginate($perPage);

        // Format dữ liệu
        $data = $inventory->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'code' => $item->product_code,
                'name' => $item->product_name,
                'brand' => $item->brand,
                'serial' => $item->serial_code ?: '--', // Nếu không có serial thì hiện --
                'provider' => $item->provider_name,
                'import_date' => $item->import_date ? Carbon::parse($item->import_date)->format('d/m/Y') : '',
                'warehouse' => $item->warehouse_name,
                'duration' => $item->storage_duration . ' ngày',
                'status' => $this->getStatusText($item->serial_status ?: $item->status, $item->warranty_date),
                'quantity' => $item->remaining_quantity // Thêm số lượng tồn để hiển thị
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $inventory->currentPage(),
                'last_page' => $inventory->lastPage(),
                'per_page' => $inventory->perPage(),
                'total' => $inventory->total(),
            ]
        ]);
    }

    // Hàm hỗ trợ hiển thị trạng thái (có thể điều chỉnh theo nghiệp vụ)
    private function getStatusText($status, $warrantyDate)
    {
        // Ví dụ: nếu hết hạn bảo hành → "Tới hạn bảo trì"
        if ($warrantyDate && Carbon::parse($warrantyDate)->isPast()) {
            return 'Tới hạn bảo trì';
        }

        $statusMap = [
            1 => 'Trong kho',
            5 => 'Đang mượn',
            // thêm các trạng thái khác nếu cần
        ];

        return $statusMap[$status] ?? '';
    }

    /**
     * Lấy danh sách nhà cung cấp để filter
     */
    public function providers(): JsonResponse
    {
        $providers = Providers::select('id', 'provider_name')
            ->orderBy('provider_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $providers
        ]);
    }

    /**
     * Lấy danh sách kho (nếu cần filter theo kho)
     */
    public function warehouses(): JsonResponse
    {
        $warehouses = Warehouse::select('id', 'warehouse_name', 'warehouse_code')
            ->orderBy('warehouse_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $warehouses
        ]);
    }
}
