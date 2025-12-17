<?php

namespace App\Http\Controllers;

use App\Imports\ProvidersImport;
use App\Models\Groups;
use App\Models\Providers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\JsonResponse;

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
     * Danh sách nhà cung cấp + tìm kiếm + lọc + sắp xếp + phân trang
     */
    public function list(Request $request): JsonResponse
    {
        $inputData = [];

        // Tìm kiếm chung
        if ($request->filled('search')) {
            $inputData['search'] = $request->search;
        }

        // Bộ lọc chi tiết từ modal
        if ($request->filled('ma')) $inputData['ma'] = $request->ma;
        if ($request->filled('ten')) $inputData['ten'] = $request->ten;
        if ($request->filled('address')) $inputData['address'] = $request->address;
        if ($request->filled('phone')) $inputData['phone'] = $request->phone;
        if ($request->filled('email')) $inputData['email'] = $request->email;
        if ($request->filled('note')) $inputData['note'] = $request->note;

        // Sắp xếp
        if ($request->has('sort_by') && $request->has('sort_dir')) {
            $sortMap = [
                'code'    => 'provider_code',
                'name'    => 'provider_name',
                'address' => 'address',
                'phone'   => 'phone',
                'email'   => 'email',
                'note'    => 'note',
            ];
            $field = $sortMap[$request->sort_by] ?? 'id';
            $inputData['sort'] = [$field, $request->sort_dir];
        }

        // Xây dựng query giống logic trong getAllProvide
        $query = DB::table('providers');

        if (!empty($inputData['search'])) {
            $search = $inputData['search'];
            $query->where(function ($q) use ($search) {
                $q->where('provider_code', 'like', "%{$search}%")
                    ->orWhere('provider_name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%");
            });
        }

        $filterable = [
            'ma'      => 'provider_code',
            'ten'     => 'provider_name',
            'address' => 'address',
            'phone'   => 'phone',
            'email'   => 'email',
            'note'    => 'note',
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
        $providers = $query->paginate($perPage);

        // Format dữ liệu trả về giống frontend
        $data = collect($providers->items())->map(function ($item) {
            return [
                'id'             => $item->id,
                'code'           => $item->provider_code,
                'name'           => $item->provider_name,
                'address'        => $item->address ?? '',
                'contact_person' => $item->contact_person ?? '',
                'phone'          => $item->phone ?? '',
                'email'          => $item->email ?? '',
                'tax_code'       => $item->tax_code ?? '',
                'note'           => $item->note ?? '',
                'group_id'       => $item->group_id,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'pagination' => [
                'current_page' => $providers->currentPage(),
                'last_page'    => $providers->lastPage(),
                'per_page'     => $providers->perPage(),
                'total'        => $providers->total(),
            ]
        ]);
    }

    /**
     * Tạo mới nhà cung cấp
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'provider_code'   => 'required|string|max:255',
            'provider_name'   => 'required|string|max:255',
            'category_id'     => 'nullable|integer|exists:groups,id', // group_id
            'address'         => 'nullable|string',
            'contact_person'  => 'nullable|string',
            'phone'           => 'nullable|string',
            'email'           => 'nullable|email',
            'tax_code'        => 'nullable|string',
            'note'            => 'nullable|string',
        ]);

        $data = $request->all();
        $data['category_id'] = $data['category_id'] ?? 0;

        $result = $this->providers->addProvide($data);

        if ($result['status'] ?? false) {
            return response()->json([
                'success' => false,
                'message' => 'Mã nhà cung cấp hoặc Tên nhà cung cấp đã tồn tại!'
            ], 422);
        }

        // Lấy bản ghi mới tạo
        $newProvider = $this->providers->find($result['id'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Tạo nhà cung cấp thành công',
            'data'    => $newProvider
        ], 201);
    }

    /**
     * Cập nhật nhà cung cấp
     */
    public function change(Request $request, $id): JsonResponse
    {
        $request->validate([
            'provider_code'   => 'required|string|max:255',
            'provider_name'   => 'required|string|max:255',
            'category_id'     => 'nullable|integer|exists:groups,id',
            'address'         => 'nullable|string',
            'contact_person'  => 'nullable|string',
            'phone'           => 'nullable|string',
            'email'           => 'nullable|email',
            'tax_code'        => 'nullable|string',
            'note'            => 'nullable|string',
        ]);

        $data = $request->all();
        $data['category_id'] = $data['category_id'] ?? 0;

        $exist = $this->providers->updateProvide($data, $id);

        if ($exist) {
            return response()->json([
                'success' => false,
                'message' => 'Mã nhà cung cấp hoặc Tên nhà cung cấp đã tồn tại ở bản ghi khác!'
            ], 422);
        }

        $updatedProvider = $this->providers->find($id);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công',
            'data'    => $updatedProvider
        ]);
    }

    /**
     * Chi tiết nhà cung cấp
     */
    public function detail($id): JsonResponse
    {
        $provider = $this->providers->find($id);

        if (!$provider) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhà cung cấp'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $provider
        ]);
    }

    /**
     * Xóa nhà cung cấp
     */
    public function delete($id): JsonResponse
    {
        $deleted = $this->providers->where('id', $id)->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy nhà cung cấp'
        ], 404);
    }

    /**
     * Lấy danh sách nhóm thuộc loại "Nhà cung cấp" để chọn khi tạo/sửa
     */
    public function groups(): JsonResponse
    {
        $groups = Groups::whereHas('type', function ($q) {
            $q->where('group_name', 'Nhà cung cấp'); // hoặc where('id', 2) nếu biết ID
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
