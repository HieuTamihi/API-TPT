<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\SerialNumber;
use App\Models\Warehouse;
use Illuminate\Http\Request;

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
}
