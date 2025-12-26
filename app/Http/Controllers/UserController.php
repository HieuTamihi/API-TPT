<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function index()
    {
        $users = User::orderByDesc('id')->get();
        $title = 'Nhân viên';
        $groups = Groups::where('group_type_id', 4)->get();
        $roles = Role::all();
        return view('setup.users.index', compact('users', 'title', 'groups', 'roles'));
    }

    public function create()
    {
        $title = 'Tạo mới nhân viên';
        $groups = Groups::where('group_type_id', 4)->get();
        $roles = Role::all();
        return view('setup.users.create', compact('title', 'groups', 'roles'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'group_id' => 'nullable|integer',
            'employee_code' => 'nullable|string',
            'role' => 'nullable|integer|exists:roles,id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        // Create a new user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'group_id' => $validated['group_id'],
            'employee_code' => $validated['employee_code'],
            'role' => $validated['role'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
        ]);

        if (!empty($validated['role'])) {
            $role = Role::findById($validated['role']); // Find role by ID
            if ($role) {
                $user->assignRole($role->name); // Assign role by name
            }
        }

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        $title = 'Chỉnh sửa nhân viên';
        $groups = Groups::where('group_type_id', 4)->get();
        $roles = Role::all();
        // Show a form to edit the user
        return view('setup.users.edit', compact('user', 'title', 'groups', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'group_id' => 'nullable|integer',
            'employee_code' => 'nullable|string',
            'role' => 'nullable|integer|exists:roles,id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        // Update the user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
            'group_id' => $validated['group_id'] ?? $user->group_id,
            'employee_code' => $validated['employee_code'],
            'address' => $validated['address'],
            'role' => $validated['role'],
            'phone' => $validated['phone'],
        ]);

        // Update the role if provided
        if (!empty($validated['role'])) {
            $role = Role::findById($validated['role']); // Find the role by ID
            if ($role) {
                $user->syncRoles([$role->name]); // Sync roles (ensures only one role)
            }
        } else {
            // If no role provided, remove all roles
            $user->syncRoles([]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        // Kiểm tra xem user_id có tồn tại trong các bảng liên quan không
        $existsInRelatedTables = DB::table('exports')->where('user_id', $user->id)->exists()
            || DB::table('imports')->where('user_id', $user->id)->exists()
            || DB::table('receiving')->where('user_id', $user->id)->exists()
            || DB::table('quotations')->where('user_id', $user->id)->exists()
            || DB::table('return_form')->where('user_id', $user->id)->exists()
            || DB::table('warehouse_transfers')->where('user_id', $user->id)->exists();

        if ($existsInRelatedTables) {
            return redirect()->back()
                ->with('warning', 'Không thể xóa nhân viên vì đang được sử dụng trong hệ thống.');
        }
        // Delete the user
        $user->delete();
        return redirect()->route('users.index');
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
        if (isset($data['roles']) && $data['roles'] !== null) {
            $filters[] = ['value' => 'Chức vụ: ' . count($data['roles']) . ' đã chọn', 'name' => 'chuc-vu', 'icon' => 'user'];
        }
        if ($request->ajax()) {
            $users = $this->users->getAllUsers($data);
            return response()->json([
                'data' => $users,
                'filters' => $filters,
            ]);
        }
        return false;
    }


    public function profile()
    {
        return view('setup.users.profile', ['user' => Auth::user()]);
    }

    /**
     * Cập nhật thông tin cá nhân.
     */
    public function update_profile(Request $request)
    {
        // Validate dữ liệu đầu vào với thông báo lỗi tùy chỉnh
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|min:6|confirmed',
        ], [
            'name.string' => 'Họ và tên phải là một chuỗi ký tự.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'email.email' => 'Email không hợp lệ.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Lấy user hiện tại
        $user = Auth::user();

        // Kiểm tra nếu chưa đăng nhập
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Bạn chưa đăng nhập.'
            ], 401);
        }

        // Cập nhật thông tin
        $user->name = $request->name;
        $user->email = $request->email;

        // Kiểm tra nếu có nhập mật khẩu mới
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Lưu thay đổi
        $user->save();

        // Chuyển hướng với thông báo thành công
        return redirect()->route('users.index')->with('msg', 'Cập nhật hồ sơ thành công.');
    }

    /**
     * Danh sách người dùng + tìm kiếm + lọc + sắp xếp + phân trang
     */
    public function list(Request $request): JsonResponse
    {
        $inputData = [];

        // Tìm kiếm chung (tên, email, phone, mã NV)
        if ($request->filled('search')) {
            $inputData['search'] = $request->search;
        }

        // Lọc chi tiết
        if ($request->filled('ma'))     $inputData['ma'] = $request->ma;      // mã nhân viên
        if ($request->filled('ten'))    $inputData['ten'] = $request->ten;   // tên
        if ($request->filled('address')) $inputData['address'] = $request->address;
        if ($request->filled('phone'))  $inputData['phone'] = $request->phone;
        if ($request->filled('email'))  $inputData['email'] = $request->email;

        // Lọc theo vai trò (roles) - có thể gửi mảng role id hoặc role name
        if ($request->filled('roles')) {
            $inputData['roles'] = $request->roles; // array
        }

        // Sắp xếp
        if ($request->has('sort_by') && $request->has('sort_dir')) {
            $sortMap = [
                'code'    => 'employee_code',
                'name'    => 'name',
                'role'    => 'roles.name',
                'address' => 'address',
                'phone'   => 'phone',
                'email'   => 'email',
            ];
            $field = $sortMap[$request->sort_by] ?? 'id';
            $inputData['sort'] = [$field, $request->sort_dir];
        }

        // Lọc theo nhóm nhân viên (group_id)
        $groupId = $request->get('group_id');

        // Xây dựng query giống logic getAllUsers của bạn
        $query = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select(
                'users.id',
                'users.name',
                'users.employee_code',
                'users.address',
                'users.phone',
                'users.email',
                'users.group_id',
                'roles.name as rolename',
                'roles.id as role_id' // <--- THÊM DÒNG NÀY: Lấy ID vai trò
            );

        // Tìm kiếm chung
        if (!empty($inputData['search'])) {
            $search = $inputData['search'];
            $query->where(function ($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc chi tiết
        $filterable = [
            'ma'      => 'employee_code',
            'ten'     => 'users.name',
            'address' => 'address',
            'phone'   => 'phone',
            'email'   => 'email',
        ];

        foreach ($filterable as $key => $field) {
            if (!empty($inputData[$key])) {
                $query->where($field, 'like', "%{$inputData[$key]}%");
            }
        }

        // Lọc theo role
        if (!empty($inputData['roles'])) {
            $query->whereIn('roles.id', $inputData['roles']);
        }

        // Lọc theo nhóm nhân viên
        if ($groupId && $groupId > 0) {
            $query->where('users.group_id', $groupId);
        }

        // Sắp xếp
        if (isset($inputData['sort'])) {
            $query->orderBy($inputData['sort'][0], $inputData['sort'][1]);
        } else {
            $query->orderBy('users.id', 'desc');
        }

        // Phân trang
        $perPage = $request->get('per_page', 20);
        $users = $query->paginate($perPage);

        // Tổng số nhân viên (cho footer)
        $totalCount = $groupId
            ? User::where('group_id', $groupId)->count()
            : User::count();

        // Format dữ liệu trả về
        $data = collect($users->items())->map(function ($item) {
            return [
                'id'             => $item->id,
                'code'           => $item->employee_code ?? '--',
                'name'           => $item->name,
                'role'           => $item->rolename ?? 'User', // Tên hiển thị
                'role_id'        => $item->role_id,            // <--- THÊM DÒNG NÀY: ID để bind vào Form sửa
                'address'        => $item->address ?? '--',
                'phone'          => $item->phone ?? '',
                'email'          => $item->email,
                'group_id'       => $item->group_id,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'total'   => $totalCount, // Dùng cho footer: "Có X nhân viên"
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'per_page'     => $users->perPage(),
                'total'        => $users->total(),
            ]
        ]);
    }

    /**
     * Xóa người dùng
     */
    public function delete($id): JsonResponse
    {
        $user = $this->users->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhân viên'
            ], 404);
        }

        // Không cho xóa chính mình hoặc super admin (tùy nghiệp vụ)
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tự xóa tài khoản đang đăng nhập'
            ], 422);
        }

        // Nếu dùng Spatie, xóa role assignments
        $user->roles()->detach();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa nhân viên thành công'
        ]);
    }

    /**
     * API: Lấy danh sách Vai trò (Roles) cho Dropdown
     */
    public function roles()
    {
        try {
            $roles = Role::select('id', 'name')->get();

            return response()->json([
                'success' => true,
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Lấy danh sách Nhóm nhân viên (Groups) cho Dropdown
     */
    public function groups()
    {
        try {
            // Lấy nhóm có type = 4 (Nhân viên) như logic trong hàm index của bạn
            $groups = Groups::where('group_type_id', 4)
                ->select('id', 'group_name', 'group_code')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $groups
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function add(Request $request)
    {
        // 1. Validate
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'group_id' => 'nullable|integer',
            'employee_code' => 'nullable|string',
            'role' => 'nullable|integer|exists:roles,id', // ID của role
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        // 2. Create User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'group_id' => $validated['group_id'] ?? 0,
            'employee_code' => $validated['employee_code'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'status' => 0, // Mặc định active
        ]);

        // 3. Assign Role (Spatie)
        if (!empty($validated['role'])) {
            $role = Role::findById($validated['role']);
            if ($role) {
                // Xóa role cũ trước khi gán mới (để chắc chắn 1 user 1 role)
                $user->syncRoles([$role->name]);
            }
        }

        // 4. Trả về kết quả (Quan trọng cho App)
        // Nếu là API request (App gọi), trả về JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo nhân viên thành công',
                'data' => $user
            ], 201);
        }

        // Nếu là Web gọi, redirect về trang index
        return redirect()->route('users.index');
    }

    public function change(Request $request, $id) // Sửa tham số thành $id cho linh hoạt
    {
        $user = User::findOrFail($id);

        // 1. Validate
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'group_id' => 'nullable|integer',
            'employee_code' => 'nullable|string',
            'role' => 'nullable|integer|exists:roles,id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        // 2. Prepare Data Update
        $dataToUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'group_id' => $validated['group_id'] ?? $user->group_id,
            'employee_code' => $validated['employee_code'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
        ];

        // Chỉ update password nếu có nhập mới
        if (!empty($validated['password'])) {
            $dataToUpdate['password'] = bcrypt($validated['password']);
        }

        $user->update($dataToUpdate);

        // 3. Sync Role
        if (!empty($validated['role'])) {
            $role = Role::findById($validated['role']);
            if ($role) {
                $user->syncRoles([$role->name]);
            }
        } else {
            // Nếu muốn cho phép user không có role thì bỏ comment dòng dưới
            // $user->syncRoles([]); 
        }

        // 4. Trả về kết quả
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nhân viên thành công',
                'data' => $user
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }
}
