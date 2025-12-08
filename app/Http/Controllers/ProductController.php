<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\Product;
use App\Models\SerialNumber;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Models\ProductWarranties;
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
}
