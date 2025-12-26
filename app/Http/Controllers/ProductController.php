<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\Product;
use App\Models\SerialNumber;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Models\ProductWarranties;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $products;
    private $productWarranty;
    public function __construct()
    {
        $this->products = new Product();
        $this->productWarranty = new ProductWarranties();
    }
    public function index()
    {
        $products = Product::orderByDesc('id')->get();
        $title = 'Hàng hoá';
        $groups = Groups::where('group_type_id', 3)->get();
        return view('setup.products.index', compact('products', 'title', 'groups'));
    }

    // Show the form for creating a new product
    public function create()
    {
        $title = 'Tạo mới hàng hoá';
        $groups = Groups::where('group_type_id', 3)->get();
        return view('setup.products.create', compact('title', 'groups'));
    }

    // Store a newly created product in storage
    public function store(Request $request)
    {
        // dd($request->all());
        $product = Product::create([
            'group_id' => $request->input('group_id'),
            'product_code' => $request->input('product_code'),
            'product_name' => $request->input('product_name'),
            'brand' => $request->input('brand'),
            'warranty' => $request->input('warranty') ?? 12,
        ]);

        $this->productWarranty->addProductWarranty($request->all(), $product->id);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    // Show the form for editing the specified product
    public function edit(Product $product)
    {
        $title = 'Chỉnh sửa hàng hoá';
        $groups = Groups::where('group_type_id', 3)->get();
        $serialNumbers = SerialNumber::where('product_id', $product->id)
            ->with(['productImports.import'])
            ->get();
        $productWarranty = ProductWarranties::where('product_id', $product->id)->get();
        return view('setup.products.edit', compact('product', 'title', 'groups', 'serialNumbers', 'productWarranty'));
    }

    // Update the specified product in storage
    public function update(Request $request, Product $product)
    {
        $product->update([
            'group_id' => $request->input('group_id'),
            'product_code' => $request->input('product_code'),
            'product_name' => $request->input('product_name'),
            'brand' => $request->input('brand'),
            'warranty' => $request->input('warranty') ?? 12,
        ]);
        $this->productWarranty->updateProductWarranty($request->all(), $product->id);
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    // Remove the specified product from storage
    public function destroy(Product $product)
    {
        // Kiểm tra xem product_id có tồn tại trong các bảng liên quan không
        $existsInRelatedTables = DB::table('product_import')->where('product_id', $product->id)->exists()
            || DB::table('product_export')->where('product_id', $product->id)->exists()
            || DB::table('received_products')->where('product_id', $product->id)->exists()
            || DB::table('product_returns')->where('product_id', $product->id)->exists()
            || DB::table('product_warranties')->where('product_id', $product->id)->exists();

        if ($existsInRelatedTables) {
            return redirect()->route('products.index')
                ->with('warning', 'Không thể xóa sản phẩm vì nó đang được sử dụng trong hệ thống.');
        }

        // Xóa sản phẩm nếu không còn liên kết
        $product->delete();

        return redirect()->route('products.index')->with('msg', 'Sản phẩm đã được xóa thành công.');
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
        if (isset($data['hang']) && $data['hang'] !== null) {
            $filters[] = ['value' => 'Hãng: ' . $data['hang'], 'name' => 'hang', 'icon' => 'po'];
        }
        if (isset($data['bao_hanh']) && $data['bao_hanh'][1] !== null) {
            $filters[] = ['value' => 'Bảo hành: ' . $data['bao_hanh'][0] . ' ' . $data['bao_hanh'][1], 'name' => 'bao-hanh', 'icon' => 'money'];
        }
        if ($request->ajax()) {
            $products = $this->products->getAllProducts($data);
            return response()->json([
                'data' => $products,
                'filters' => $filters,
            ]);
        }
        return false;
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new ProductsImport();
        Excel::import($import, $request->file('file'));

        // Nếu có dữ liệu trùng lặp, chuyển đến view hiển thị danh sách trùng
        if (!empty($import->duplicates)) {
            return view('setup.products.duplicates', [
                'duplicates' => $import->duplicates,
                'title' => 'Dữ liệu sản phẩm trùng lặp',
            ]);
        }

        return redirect()->back()->with('success', 'Import sản phẩm thành công!');
    }
    public function bulkConfirm(Request $request)
    {
        // Kiểm tra nếu không có sản phẩm nào được chọn thì bỏ qua
        if (!$request->has('products') || empty($request->input('products'))) {
            return redirect()->route('products.index')->with('warning', 'Không có sản phẩm nào được chọn để cập nhật!');
        }

        $products = $request->input('products');

        foreach ($products as $productData) {
            $productData = json_decode($productData, true);
            $productId = $productData['product_id'];
            $rowData = $productData['row_data'];
            $product = Product::find($productId);

            if ($product) {
                $product->update([
                    'product_code' => $rowData[0], // Mã sản phẩm
                    'product_name' => $rowData[1], // Tên sản phẩm
                    'brand'        => $rowData[2], // Thương hiệu
                    'warranty'     => $rowData[3], // Bảo hành
                ]);
            }
        }
        return redirect()->route('products.index')->with('msg', 'Cập nhật hàng loạt sản phẩm thành công!');
    }

    /**
     * API: Danh sách Hàng hóa
     */
    public function list(Request $request): JsonResponse
    {
        $query = Product::with('group');

        // 1. Tìm kiếm chung
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_code', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // 2. Lọc chi tiết
        if ($request->filled('ma')) $query->where('product_code', 'like', "%{$request->ma}%");
        if ($request->filled('ten')) $query->where('product_name', 'like', "%{$request->ten}%");
        if ($request->filled('brand')) $query->where('brand', 'like', "%{$request->brand}%");
        if ($request->filled('group_id')) $query->where('group_id', $request->group_id);

        // 3. Sắp xếp
        if ($request->has('sort_by') && $request->has('sort_dir')) {
            $sortBy = $request->sort_by;
            if ($sortBy === 'code') $sortBy = 'product_code';
            if ($sortBy === 'name') $sortBy = 'product_name';
            $query->orderBy($sortBy, $request->sort_dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        // 4. Phân trang
        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * API: Thêm mới Hàng hóa
     */
    public function add(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_code' => 'required|string|max:50|unique:products,product_code',
            'product_name' => 'required|string|max:255',
            'group_id' => 'nullable|integer|exists:groups,id',
            'brand' => 'nullable|string|max:100',
            'warranty' => 'nullable|integer|min:0', // Tháng bảo hành
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        try {
            $product = Product::create($request->all());
            return response()->json(['success' => true, 'message' => 'Thêm hàng hóa thành công', 'data' => $product], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: Cập nhật Hàng hóa
     */
    public function change(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['success' => false, 'message' => 'Không tìm thấy hàng hóa'], 404);

        $validator = Validator::make($request->all(), [
            'product_code' => 'required|string|max:50|unique:products,product_code,' . $id,
            'product_name' => 'required|string|max:255',
            'group_id' => 'nullable|integer|exists:groups,id',
            'warranty' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        $product->update($request->all());
        return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'data' => $product]);
    }

    /**
     * API: Xóa Hàng hóa
     */
    public function delete($id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['success' => false, 'message' => 'Không tìm thấy hàng hóa'], 404);

        // Kiểm tra ràng buộc: Đã có Serial, đã nhập kho, xuất kho...
        $hasData = $product->serialNumbers()->exists()
            || DB::table('product_import')->where('product_id', $id)->exists()
            || DB::table('product_export')->where('product_id', $id)->exists();

        if ($hasData) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa hàng hóa đã phát sinh giao dịch.'], 422);
        }

        $product->delete();
        return response()->json(['success' => true, 'message' => 'Xóa thành công']);
    }

    /**
     * API: Lấy nhóm Hàng hóa (Loại 3)
     */
    public function groups(): JsonResponse
    {
        $groups = Groups::where('group_type_id', 3)->select('id', 'group_name')->get();
        return response()->json(['success' => true, 'data' => $groups]);
    }
}
