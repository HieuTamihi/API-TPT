<?php

namespace App\Http\Controllers;

use App\Imports\CustomersImport;
use App\Models\Customers;
use App\Models\Groups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\JsonResponse;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $customers;

    public function __construct()
    {
        $this->customers = new Customers();
    }
    public function index()
    {
        $title = "Khách hàng";
        $customers = $this->customers->getAllGuest();
        $count = $customers->where('group_id', 0)->count();
        $groups = Groups::where('group_type_id', 1)->get();
        return view('setup.customers.index', compact('title', 'customers', 'groups', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = "Thêm mới khách hàng";
        $groups = Groups::where('group_type_id', 1)->get();
        return view('setup.customers.create', compact('title', 'groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $result = $this->customers->addGuest($request->all());
        if ($result == true) {
            $msg = redirect()->back()->with('warning', 'Mã khách hàng hoặc tên khách hàng đã tồn tại!');
        } else {
            $msg = redirect()->route('customers.index')->with('msg', 'Thêm mới khách hàng thành công');
        }
        return $msg;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = "Xem chi tiết khách hàng";
        $customer = Customers::where('customers.id', $id)
            ->leftJoin('groups', 'customers.group_id', 'groups.id')
            ->select('customers.*', 'groups.group_name')
            ->first();
        return view('setup.customers.show', compact(
            'customer',
            'title'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = "Chỉnh sửa khách hàng";
        $customer = Customers::where('customers.id', $id)
            ->leftJoin('groups', 'customers.group_id', 'groups.id')
            ->select('customers.*', 'groups.group_name')
            ->first();
        $groups = Groups::where('group_type_id', 1)->get();
        return view('setup.customers.edit', compact('customer', 'title', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $guests = Customers::where('id', '!=', $id)
            ->where(function ($query) use ($request) {
                $query->where('customer_code', $request->customer_code)
                    ->orWhere('customer_name', $request->customer_name);
            })
            ->first();
        if ($guests) {
            return back()->with('warning', 'Mã khách hàng hoặc tên khách hàng đã tồn tại!');
        } else {
            $data = [
                'customer_code' => $request->customer_code,
                'customer_name' => $request->customer_name,
                'address' => $request->address,
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
                'email' => $request->email,
                'tax_code' => $request->tax_code,
                'note' => $request->note,
                'group_id' => $request->grouptype_id,
                'updated_at' => now(),
            ];
            $this->customers->updateCustomer($data, $id);
            return redirect(route('customers.index'))->with('msg', 'Sửa khách hàng thành công');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customers::find($id);
        if (!$customer) {
            return back()->with('warning', 'Không tìm thấy khách hàng để xóa');
        }
        // Kiểm tra xem customer_id có tồn tại trong các bảng liên quan không
        $existsInRelatedTables = DB::table('exports')->where('customer_id', $id)->exists()
            || DB::table('warranty_lookup')->where('customer_id', $id)->exists()
            || DB::table('receiving')->where('customer_id', $id)->exists()
            || DB::table('quotations')->where('customer_id', $id)->exists()
            || DB::table('return_form')->where('customer_id', $id)->exists();

        if ($existsInRelatedTables) {
            return redirect()->back()
                ->with('warning', 'Không thể xóa khách hàng vì đang được sử dụng trong hệ thống.');
        }
        $customer->delete();
        return back()->with('msg', 'Xóa khách hàng thành công');
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
            $customers = $this->customers->getAllGuest($data);
            return response()->json([
                'data' => $customers,
                'filters' => $filters,
            ]);
        }
        return false;
    }

    // Import 
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $import = new CustomersImport();
        Excel::import($import, $request->file('file'));

        // If there are duplicates, return them to the view
        if (!empty($import->duplicates)) {
            return view('setup.customers.duplicates', [
                'duplicates' => $import->duplicates,
                'title' => 'Dữ liệu trùng lặp',
            ]);
        }

        return redirect()->back()->with('success', 'Import thành công!');
    }
    public function bulkConfirm(Request $request)
    {
        // Lấy danh sách khách hàng được chọn
        $customers = $request->input('customers', []);
        // Nếu không có khách hàng nào được chọn, quay lại mà không làm gì
        if (empty($customers)) {
            return redirect()->route('customers.index')->with('info', 'Không có khách hàng nào được chọn.');
        }

        foreach ($customers as $customerData) {
            $customerData = json_decode($customerData, true);
            $customerId = $customerData['customer_id'];
            $rowData = $customerData['row_data'];
            $customer = Customers::find($customerId);

            if ($customer) {
                $customer->update([
                    'customer_code'  => $rowData[0],  // Mã khách hàng
                    'customer_name'  => $rowData[1],  // Tên khách hàng
                    'address'        => $rowData[2],  // Địa chỉ
                    'contact_person' => $rowData[3],  // Người liên hệ
                    'phone'          => $rowData[4],  // Số điện thoại
                    'email'          => $rowData[5],  // Email
                    'tax_code'       => $rowData[6],  // Mã số thuế
                    'note'           => $rowData[7],  // Ghi chú
                ]);
            }
        }

        return redirect()->route('customers.index')->with('success', 'Cập nhật hàng loạt thành công!');
    }

    /**
     * Danh sách khách hàng + tìm kiếm + lọc + sắp xếp + phân trang
     */
    public function list(Request $request): JsonResponse
    {
        // Chuẩn bị dữ liệu đầu vào theo đúng format mà method getAllGuest đang mong đợi
        $inputData = [];

        // Tìm kiếm chung
        if ($request->filled('search')) {
            $inputData['search'] = $request->search;
        }

        // Các bộ lọc chi tiết từ modal filter
        if ($request->filled('ma')) $inputData['ma'] = $request->ma;
        if ($request->filled('ten')) $inputData['ten'] = $request->ten;
        if ($request->filled('address')) $inputData['address'] = $request->address;
        if ($request->filled('phone')) $inputData['phone'] = $request->phone;
        if ($request->filled('email')) $inputData['email'] = $request->email;
        if ($request->filled('note')) $inputData['note'] = $request->note;

        // Sắp xếp: sort_by và sort_dir từ frontend
        if ($request->has('sort_by') && $request->has('sort_dir')) {
            $sortFieldMap = [
                'code'     => 'customer_code',
                'name'     => 'customer_name',
                'address'  => 'address',
                'phone'    => 'phone',
                'email'    => 'email',
            ];
            $field = $sortFieldMap[$request->sort_by] ?? 'id';
            $inputData['sort'] = [$field, $request->sort_dir]; // asc hoặc desc
        }

        // Lấy dữ liệu từ model (chưa phân trang ở model → ta sẽ xử lý phân trang ở đây)
        $query = $this->customers->newQuery();

        if (!empty($inputData)) {
            // Tái sử dụng logic tìm kiếm + lọc + sắp xếp từ model
            // Chúng ta sẽ copy logic để hỗ trợ phân trang
            if (isset($inputData['search'])) {
                $search = $inputData['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('customer_code', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%");
                });
            }

            $filterable = [
                'ma'      => 'customer_code',
                'ten'     => 'customer_name',
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
        } else {
            $query->orderBy('id', 'desc');
        }

        // Phân trang
        $perPage = $request->get('per_page', 20);
        $customers = $query->paginate($perPage);

        // Format dữ liệu trả về giống frontend mong đợi
        $data = collect($customers->items())->map(function ($item) {
            return [
                'id'              => $item->id,
                'code'            => $item->customer_code,
                'name'            => $item->customer_name,
                'address'         => $item->address ?? '',
                'contact_person'  => $item->contact_person ?? '',
                'phone'           => $item->phone ?? '',
                'email'           => $item->email ?? '',
                'tax_code'        => $item->tax_code ?? '',
                'note'            => $item->note ?? '',
                'group_id'        => $item->group_id,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'pagination' => [
                'current_page' => $customers->currentPage(),
                'last_page'    => $customers->lastPage(),
                'per_page'     => $customers->perPage(),
                'total'        => $customers->total(),
            ]
        ]);
    }

    /**
     * Tạo mới khách hàng (kiểm tra trùng mã hoặc tên)
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'customer_code'   => 'required|string|max:255',
            'customer_name'   => 'required|string|max:255',
            'group_id'        => 'nullable|integer|exists:groups,id',
            'address'         => 'nullable|string',
            'contact_person'  => 'nullable|string',
            'phone'           => 'nullable|string',
            'email'           => 'nullable|email',
            'tax_code'        => 'nullable|string',
            'note'            => 'nullable|string',
        ]);

        $data = $request->all();
        $data['grouptype_id'] = $data['group_id'] ?? 0; // tương thích với model cũ

        $exist = $this->customers->addGuest($data);

        if ($exist) {
            return response()->json([
                'success' => false,
                'message' => 'Mã khách hàng hoặc Tên khách hàng đã tồn tại!'
            ], 422);
        }

        // Lấy lại bản ghi vừa tạo để trả về
        $newCustomer = $this->customers
            ->where('customer_code', $request->customer_code)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Tạo khách hàng thành công',
            'data'    => $newCustomer
        ], 201);
    }

    /**
     * Cập nhật khách hàng
     */
    public function change(Request $request, $id): JsonResponse
    {
        $request->validate([
            'customer_code'   => 'required|string|max:255',
            'customer_name'   => 'required|string|max:255',
            'group_id'        => 'nullable|integer|exists:groups,id',
            'address'         => 'nullable|string',
            'contact_person'  => 'nullable|string',
            'phone'           => 'nullable|string',
            'email'           => 'nullable|email',
            'tax_code'        => 'nullable|string',
            'note'            => 'nullable|string',
        ]);

        $data = $request->all();
        if ($request->has('group_id')) {
            $data['group_id'] = $request->group_id;
        }

        $updated = $this->customers->updateCustomer($data, $id);

        if ($updated) {
            $customer = $this->customers->find($id);
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công',
                'data'    => $customer
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy khách hàng'
        ], 404);
    }

    /**
     * Chi tiết khách hàng
     */
    public function detail($id): JsonResponse
    {
        $customer = $this->customers->find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khách hàng'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $customer
        ]);
    }

    /**
     * Xóa khách hàng (nếu cần)
     */
    public function detele($id): JsonResponse
    {
        $deleted = $this->customers->where('id', $id)->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa thành công'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy khách hàng'
        ], 404);
    }

    /**
     * Lấy danh sách nhóm thuộc loại "Khách hàng" để chọn khi tạo/sửa
     */
    public function groups(): JsonResponse
    {
        $groups = Groups::whereHas('type', function ($q) {
            $q->where('group_name', 'Khách hàng'); // hoặc where('id', 1)
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
