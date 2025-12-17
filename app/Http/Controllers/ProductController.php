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
     * Danh sách sản phẩm + tìm kiếm + lọc + sắp xếp + phân trang
     * Hỗ trợ lọc theo nhóm (group_id) nếu frontend gửi
     */
    public function list(Request $request): JsonResponse
    {
        $inputData = [];

        // Tìm kiếm chung
        if ($request->filled('search')) {
            $inputData['search'] = $request->search;
        }

        // Lọc chi tiết
        if ($request->filled('ma'))     $inputData['ma'] = $request->ma;
        if ($request->filled('ten'))    $inputData['ten'] = $request->ten;
        if ($request->filled('hang'))   $inputData['hang'] = $request->hang;

        // Lọc khoảng bảo hành (min và max)
        if ($request->filled('warranty_min') || $request->filled('warranty_max')) {
            $min = $request->get('warranty_min', 0);
            $max = $request->get('warranty_max', 999);
            $inputData['bao_hanh'] = [$min, $max]; // tương thích với logic model
        }

        // Sắp xếp
        if ($request->has('sort_by') && $request->has('sort_dir')) {
            $sortMap = [
                'code' => 'product_code',
                'name' => 'product_name',
                'brand' => 'brand',
            ];
            $field = $sortMap[$request->sort_by] ?? 'id';
            $inputData['sort'] = [$field, $request->sort_dir]; // asc/desc
        }

        // Lọc theo nhóm hàng hóa (nếu frontend gửi group_id)
        $groupId = $request->get('group_id');

        // Xây dựng query giống logic getAllProducts
        $query = DB::table('products');

        if (!empty($inputData['search'])) {
            $search = $inputData['search'];
            $query->where(function ($q) use ($search) {
                $q->where('product_code', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        $filterable = [
            'ma'   => 'product_code',
            'ten'  => 'product_name',
            'hang' => 'brand',
        ];

        foreach ($filterable as $key => $field) {
            if (!empty($inputData[$key])) {
                $query->where($field, 'like', "%{$inputData[$key]}%");
            }
        }

        // Lọc bảo hành
        if (isset($inputData['bao_hanh'])) {
            $query->whereBetween('warranty', $inputData['bao_hanh']);
        }

        // Lọc theo nhóm
        if ($groupId && $groupId > 0) {
            $query->where('group_id', $groupId);
        }

        // Sắp xếp
        if (isset($inputData['sort'])) {
            $query->orderBy($inputData['sort'][0], $inputData['sort'][1]);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Phân trang
        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        // Đếm tổng số lượng (cho footer: SL hàng hoá)
        $totalCount = $groupId ? 
            DB::table('products')->where('group_id', $groupId)->count() : 
            DB::table('products')->count();

        // Format dữ liệu
        $data = collect($products->items())->map(function ($item) {
            return [
                'id'           => $item->id,
                'code'         => $item->product_code,
                'name'         => $item->product_name,
                'brand'        => $item->brand ?? '',
                'warranty'     => $item->warranty,
                'group_id'     => $item->group_id,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'total'   => $totalCount, // Dùng cho footer: SL hàng hoá
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ]
        ]);
    }

    /**
     * Xóa sản phẩm
     */
    public function delete($id): JsonResponse
    {
        $product = $this->products->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm'
            ], 404);
        }

        // Kiểm tra có serial hoặc tồn kho không trước khi xóa (tùy nghiệp vụ)
        // Ví dụ: nếu có serial thì không cho xóa
        $hasSerial = DB::table('serial_numbers')->where('product_id', $id)->exists();
        if ($hasSerial) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa sản phẩm đã có serial/phiếu nhập xuất'
            ], 422);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa sản phẩm thành công'
        ]);
    }

    /**
     * Lấy danh sách nhóm thuộc loại "Hàng hóa" (group_types.id = 3)
     */
    public function groups(): JsonResponse
    {
        $groups = Groups::whereHas('type', function ($q) {
            $q->where('group_name', 'Hàng hóa'); // hoặc where('id', 3)
        })
            ->select('id', 'group_code', 'group_name')
            ->orderBy('group_name')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $groups
        ]);
    }
}
