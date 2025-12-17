<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\SerialNumber;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $warehouse;

    public function __construct()
    {
        $this->warehouse = new Warehouse();
    }
    public function index()
    {
        $warehouses = Warehouse::all();
        $title = 'Kho';
        return view('setup.warehouses.index', compact('warehouses', 'title'));
    }

    public function create()
    {
        $title = 'Thêm kho';
        return view('setup.warehouses.create', compact('title'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'nullable|integer',
            'warehouse_code' => 'required|string|max:255|unique:warehouses,warehouse_code',
            'warehouse_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse created successfully.');
    }

    public function show(Warehouse $warehouse)
    {
        return view('warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        $title = 'Sửa thông tin kho';
        $serialNumbers = SerialNumber::where('warehouse_id', $warehouse->id)->get();
        return view('setup.warehouses.edit', compact('warehouse', 'title', 'serialNumbers'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'type' => 'nullable|integer',
            'warehouse_code' => 'required|string|max:255|unique:warehouses,warehouse_code,' . $warehouse->id,
            'warehouse_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted successfully.');
    }
    public function filterData(Request $request)
    {
        $data = $request->all();
        $filters = [];
        if (isset($data['ma']) && $data['ma'] !== null) {
            $filters[] = ['value' => 'Mã: ' . $data['ma'], 'name' => 'ma', 'icon' => 'po'];
        }
        if (isset($data['ten']) && $data['ten'] !== null) {
            $filters[] = ['value' => 'Tên: ' . $data['ten'], 'name' => 'ten', 'icon' => 'po'];
        }
        if (isset($data['address']) && $data['address'] !== null) {
            $filters[] = ['value' => 'Địa chỉ: ' . $data['address'], 'name' => 'dia-chi', 'icon' => 'po'];
        }
        if ($request->ajax()) {
            $warehouse = $this->warehouse->getAllWarehouse($data);
            return response()->json([
                'data' => $warehouse,
                'filters' => $filters,
            ]);
        }
        return false;
    }

    /**
     * Danh sách kho + tìm kiếm + lọc + sắp xếp + phân trang
     */
    public function list(Request $request): JsonResponse
    {
        $inputData = [];

        // Tìm kiếm chung
        if ($request->filled('search')) {
            $inputData['search'] = $request->search;
        }

        // Lọc chi tiết từ modal (nếu có)
        if ($request->filled('ma'))      $inputData['ma'] = $request->ma;
        if ($request->filled('ten'))     $inputData['ten'] = $request->ten;
        if ($request->filled('address')) $inputData['address'] = $request->address;

        // Sắp xếp
        if ($request->has('sort_by') && $request->has('sort_dir')) {
            $sortMap = [
                'code' => 'warehouse_code',
                'name' => 'warehouse_name',
            ];
            $field = $sortMap[$request->sort_by] ?? 'id';
            $inputData['sort'] = [$field, $request->sort_dir];
        }

        // Xây dựng query giống logic getAllWarehouse
        $query = DB::table('warehouses');

        if (!empty($inputData['search'])) {
            $search = $inputData['search'];
            $query->where(function ($q) use ($search) {
                $q->where('warehouse_code', 'like', "%{$search}%")
                    ->orWhere('warehouse_name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $filterable = [
            'ma'      => 'warehouse_code',
            'ten'     => 'warehouse_name',
            'address' => 'address',
        ];

        foreach ($filterable as $key => $field) {
            if (!empty($inputData[$key])) {
                $query->where($field, 'like', "%{$inputData[$key]}%");
            }
        }

        if (isset($inputData['sort'])) {
            $query->orderBy($inputData['sort'][0], $inputData['sort'][1]);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Phân trang
        $perPage = $request->get('per_page', 20);
        $warehouses = $query->paginate($perPage);

        // Format dữ liệu trả về giống frontend
        $data = collect($warehouses->items())->map(function ($item) {
            return [
                'id'             => $item->id,
                'code'           => $item->warehouse_code,
                'name'           => $item->warehouse_name ?? '',
                'address'        => $item->address ?? '',
                'type'           => $item->type,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'pagination' => [
                'current_page' => $warehouses->currentPage(),
                'last_page'    => $warehouses->lastPage(),
                'per_page'     => $warehouses->perPage(),
                'total'        => $warehouses->total(),
            ]
        ]);
    }

    /**
     * Chi tiết kho (dùng khi nhấn vào dòng để chỉnh sửa)
     */
    public function detail($id): JsonResponse
    {
        $warehouse = $this->warehouse->find($id);

        if (!$warehouse) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kho'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'              => $warehouse->id,
                'code'            => $warehouse->warehouse_code,
                'name'            => $warehouse->warehouse_name,
                'address'         => $warehouse->address,
                'type'            => $warehouse->type,
            ]
        ]);
    }

    /**
     * Tạo mới kho
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'warehouse_code' => 'required|string|max:255|unique:warehouses,warehouse_code',
            'warehouse_name' => 'required|string|max:255',
            'address'        => 'nullable|string',
            'type'           => 'required|integer',
        ]);

        $warehouse = $this->warehouse->create($request->only([
            'type',
            'warehouse_code',
            'warehouse_name',
            'address'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Tạo kho thành công',
            'data'    => $warehouse
        ], 201);
    }

    /**
     * Cập nhật kho
     */
    public function change(Request $request, $id): JsonResponse
    {
        $warehouse = $this->warehouse->find($id);

        if (!$warehouse) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kho'
            ], 404);
        }

        $request->validate([
            'warehouse_code' => 'required|string|max:255|unique:warehouses,warehouse_code,' . $id,
            'warehouse_name' => 'required|string|max:255',
            'address'        => 'nullable|string',
            'type'           => 'required|integer',
        ]);

        $warehouse->update($request->only([
            'type',
            'warehouse_code',
            'warehouse_name',
            'address'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật kho thành công',
            'data'    => $warehouse->fresh()
        ]);
    }

    /**
     * Xóa kho (nếu cần sau này)
     */
    public function delete($id): JsonResponse
    {
        $warehouse = $this->warehouse->find($id);

        if (!$warehouse) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kho'
            ], 404);
        }

        // Kiểm tra ràng buộc (ví dụ: kho đang có hàng, phiếu nhập/xuất...)
        // $hasStock = DB::table('serial_numbers')->where('warehouse_id', $id)->exists();
        // if ($hasStock) { ... return error ... }

        $warehouse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa kho thành công'
        ]);
    }
}
