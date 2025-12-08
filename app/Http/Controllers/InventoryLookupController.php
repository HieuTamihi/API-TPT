<?php

namespace App\Http\Controllers;

use App\Helpers\GlobalHelper;
use App\Models\InventoryHistory;
use App\Models\InventoryLookup;
use App\Models\Product;
use App\Models\Providers;
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
}
