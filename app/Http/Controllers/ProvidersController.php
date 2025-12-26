<?php

namespace App\Http\Controllers;

use App\Imports\ProvidersImport;
use App\Models\Groups;
use App\Models\Providers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProvidersController extends Controller
{
    private $providers;
    public function __construct()
    {
        $this->providers = new Providers();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Nhà cung cấp";
        $dataa = $this->providers->getAllProvide();

        $groups = Groups::where('group_type_id', 2)->get();

        $providers = Providers::where('group_id', 0)->orderByDesc('id')->get();
        return view('setup.providers.index', compact('title', 'providers', 'dataa', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = "Thêm mới nhà cung cấp";
        $category = Groups::where('group_type_id', 2)->get();
        return view('setup.providers.create', compact('title', 'category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $result = $this->providers->addProvide($request->all());
        if ($result['status'] == true) {
            $msg = redirect()->back()->with('warning', 'Mã nhà cung cấp hoặc tên nhà cung cấp đã tồn tại!');
        } else {
            $msg = redirect()->route('providers.index')->with('msg', 'Thêm mới nhà cung cấp thành công');
        }
        return $msg;
    }

    /**
     * Display the specified resource.
     */
    public function show(Providers $providers)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, Request $request)
    {
        $provider = Providers::findOrFail($id);
        $title = "Chỉnh sửa nhà cung cấp";
        $category = Groups::where('group_type_id', 2)->get();

        return view('setup.providers.edit', compact('title', 'provider', 'category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $status =  $this->providers->updateProvide($request->all(), $id);
        if ($status) {
            return redirect()->back()->with('warning', 'Mã nhà cung cấp hoặc tên nhà cung cấp đã tồn tại!');
        } else {
            return redirect(route('providers.index'))->with('msg', 'Sửa nhà cung cấp thành công');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $provider = Providers::find($id);
        if (!$provider) {
            return back()->with('warning', 'Không tìm thấy nhà cung cấp để xóa');
        }
        // $check = DetailExport::where('guest_id', $id)->first();
        // if ($check) {
        //     return back()->with('warning', 'Xóa thất bại do khách hàng này đang báo giá!');
        // }
        // Kiểm tra xem provider_id có tồn tại trong các bảng liên quan không
        $existsInRelatedTables = DB::table('imports')->where('provider_id', $id)->exists()
            || DB::table('inventory_lookup')->where('provider_id', $id)->exists();

        if ($existsInRelatedTables) {
            return redirect()->back()
                ->with('warning', 'Không thể xóa nhà cung cấp vì đang được sử dụng trong hệ thống.');
        }
        $provider->delete();
        return back()->with('msg', 'Xóa nhà cung cấp thành công');
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
        if (isset($data['phone']) && $data['phone'] !== null) {
            $filters[] = ['value' => 'Điện thoại: ' . $data['phone'], 'name' => 'dien-thoai', 'icon' => 'po'];
        }
        if (isset($data['email']) && $data['email'] !== null) {
            $filters[] = ['value' => 'Email: ' . $data['email'], 'name' => 'email', 'icon' => 'po'];
        }
        if (isset($data['note']) && $data['note'] !== null) {
            $filters[] = ['value' => 'Ghi chú: ' . $data['note'], 'name' => 'ghi-chu', 'icon' => 'po'];
        }
        if ($request->ajax()) {
            $providers = $this->providers->getAllProvide($data);
            return response()->json([
                'data' => $providers,
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

        $import = new ProvidersImport();
        Excel::import($import, $request->file('file'));

        // If there are duplicates, return them to the view
        if (!empty($import->duplicates)) {
            return view('setup.providers.duplicates', [
                'duplicates' => $import->duplicates,
                'title' => 'Dữ liệu trùng lặp',
            ]);
        }

        return redirect()->back()->with('success', 'Import thành công!');
    }
    public function bulkConfirm(Request $request)
    {
        // Lấy danh sách nhà cung cấp được chọn
        $providers = $request->input('providers', []);

        // Nếu không có nhà cung cấp nào được chọn, quay lại mà không làm gì
        if (empty($providers)) {
            return redirect()->route('providers.index')->with('info', 'Không có nhà cung cấp nào được chọn.');
        }

        foreach ($providers as $providerData) {
            $providerData = json_decode($providerData, true);
            $providerId = $providerData['provider_id'];
            $rowData = $providerData['row_data'];
            $provider = Providers::find($providerId);

            if ($provider) {
                $provider->update([
                    'provider_code'  => $rowData[0],
                    'provider_name'  => $rowData[1],
                    'contact_person' => $rowData[2],
                    'address'        => $rowData[3],
                    'phone'          => $rowData[4],
                    'email'          => $rowData[5],
                    'tax_code'       => $rowData[6],
                    'note'           => $rowData[7],
                ]);
            }
        }

        return redirect()->route('providers.index')->with('success', 'Cập nhật hàng loạt thành công!');
    }

    /**
     * API: Danh sách NCC
     */
    public function list(Request $request): JsonResponse
    {
        $query = Providers::with('group');

        // 1. Tìm kiếm chung
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('provider_code', 'like', "%{$search}%")
                    ->orWhere('provider_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 2. Lọc chi tiết
        if ($request->filled('ma')) $query->where('provider_code', 'like', "%{$request->ma}%");
        if ($request->filled('ten')) $query->where('provider_name', 'like', "%{$request->ten}%");
        if ($request->filled('phone')) $query->where('phone', 'like', "%{$request->phone}%");
        if ($request->filled('group_id')) $query->where('group_id', $request->group_id);

        // 3. Sắp xếp
        if ($request->has('sort_by') && $request->has('sort_dir')) {
            $sortBy = $request->sort_by;
            if ($sortBy === 'code') $sortBy = 'provider_code';
            if ($sortBy === 'name') $sortBy = 'provider_name';
            $query->orderBy($sortBy, $request->sort_dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        // 4. Phân trang
        $perPage = $request->get('per_page', 20);
        $providers = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $providers->items(),
            'pagination' => [
                'current_page' => $providers->currentPage(),
                'last_page' => $providers->lastPage(),
                'per_page' => $providers->perPage(),
                'total' => $providers->total(),
            ]
        ]);
    }

    /**
     * API: Thêm mới NCC
     */
    public function add(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider_code' => 'required|string|max:50|unique:providers,provider_code',
            'provider_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'group_id' => 'nullable|integer|exists:groups,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        try {
            $provider = Providers::create($request->all());
            return response()->json(['success' => true, 'message' => 'Thêm nhà cung cấp thành công', 'data' => $provider], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: Cập nhật NCC
     */
    public function change(Request $request, $id): JsonResponse
    {
        $provider = Providers::find($id);
        if (!$provider) return response()->json(['success' => false, 'message' => 'Không tìm thấy NCC'], 404);

        $validator = Validator::make($request->all(), [
            'provider_code' => 'required|string|max:50|unique:providers,provider_code,' . $id,
            'provider_name' => 'required|string|max:255',
            'group_id' => 'nullable|integer|exists:groups,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        $provider->update($request->all());
        return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'data' => $provider]);
    }

    /**
     * API: Xóa NCC
     */
    public function delete($id): JsonResponse
    {
        $provider = Providers::find($id);
        if (!$provider) return response()->json(['success' => false, 'message' => 'Không tìm thấy NCC'], 404);

        // Kiểm tra ràng buộc
        $hasData = DB::table('imports')->where('provider_id', $id)->exists()
            || DB::table('inventory_lookup')->where('provider_id', $id)->exists();

        if ($hasData) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa NCC đã có giao dịch nhập hàng.'], 422);
        }

        $provider->delete();
        return response()->json(['success' => true, 'message' => 'Xóa thành công']);
    }

    /**
     * API: Lấy nhóm NCC (Loại 2)
     */
    public function groups(): JsonResponse
    {
        $groups = Groups::where('group_type_id', 2)->select('id', 'group_name')->get();
        return response()->json(['success' => true, 'data' => $groups]);
    }
}
