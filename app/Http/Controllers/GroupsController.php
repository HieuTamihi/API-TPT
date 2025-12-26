<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Groups;
use App\Models\Grouptype;
use App\Models\GrouptypeMain;
use App\Models\Product;
use App\Models\Providers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $groups;

    public function __construct()
    {
        $this->groups = new Groups();
    }
    public function index()
    {
        $title = 'Nhóm đối tượng';
        $groups = $this->groups->getAll();
        $groupedGroups = $this->groups->getAllGroupedByType();
        return view('setup.groups.index', compact('title', 'groups', 'groupedGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $grouptypes = GrouptypeMain::all();
        $title = "Thêm mới nhóm đối tượng";
        return view('setup.groups.create', compact('title', 'grouptypes'));
    }
    public function dataObj(Request $request)
    {
        $data = $request->all();
        $dataGroup = $this->groups->dataObj($data['group_id']);
        return $dataGroup;
    }
    public function updateDataGroup(Request $request)
    {
        $data = $request->all();
        $dataGroup = $this->groups->updateDataGroup($data);
        return $dataGroup;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Kiểm tra tồn tại
        $existingGroup = Groups::where('group_code', $request->group_code)
            ->where('group_type_id', $request->group_type_id)
            ->first();

        if ($existingGroup) {
            return redirect()->back()->with('warning', 'Mã đối tượng đã tồn tại trong loại nhóm được chọn');
        } else {
            // Thêm mới nếu chưa tồn tại
            $datagroup = [
                'group_code'    => $request->group_code,
                'group_name'    => $request->group_name,
                'group_type_id' => $request->group_type_id,
                'description'   => $request->description,
                'created_at'    => now(),
            ];

            DB::table('groups')->insertGetId($datagroup);

            return redirect()->route('groups.index')->with('msg', 'Thêm mới nhóm thành công!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $group = Groups::where('id', $id)
            ->first();
        if ($group) {
            $title = $group->name;
        } else {
            abort('404');
            $title = '';
            return view('setup.groups.show', compact('title', 'group'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, Request $request)
    {
        $group = Groups::where('id', $id)
            ->first();
        if ($group) {
            $title = $group->name;
        } else {
            abort('404');
            $title = '';
        }
        $getId = $id;
        $request->session()->put('idGr', $id);
        $grouptypes = GrouptypeMain::all();
        $dataGroup = $this->groups->getDataGroup($id);
        return view('setup.groups.edit', compact('title', 'group', 'dataGroup', 'grouptypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = session('idGr');
        $currentGroup = $this->groups->find($id);
        $data = [
            'group_code' => $request->group_code,
            'group_name' => $request->group_name_display,
            'description' => $request->group_desc,
        ];
        if (!empty($request->group_type_id)) {
            $data['group_type_id'] = $request->group_type_id;
        } else {
            $data['group_type_id'] = $currentGroup->group_type_id;
        }
        $existingGroup = Groups::where('group_code', $request->group_code)
            ->where('group_type_id', $data['group_type_id'])
            ->where('id', '!=', $id)
            ->first();
        if ($existingGroup) {
            return redirect()->back()->with('warning', 'Mã đối tượng đã tồn tại trong loại nhóm được chọn');
        } else {
            $this->groups->updateGroup($data, $id);
            session()->forget('idGr');
            return redirect(route('groups.index'))->with('msg', 'Sửa nhóm đối tượng thành công');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $group = Groups::find($id);
        if (!$group) {
            return back()->with('warning', 'Không tìm thấy loại đối tượng để xóa.');
        }
        // Mảng các điều kiện kiểm tra
        $conditions = [
            1 => Customers::where('group_id', $id)->first(),
            2 => Providers::where('group_id', $id)->first(),
            3 => Product::where('group_id', $id)->first(),
            4 => User::where('group_id', $id)->first(),
        ];
        // Kiểm tra nếu group type tồn tại và có bản ghi sử dụng group_id
        if (isset($conditions[$group->group_type_id]) && $conditions[$group->group_type_id]) {
            return back()->with('warning', 'Xóa thất bại do loại đối tượng vẫn đang còn sử dụng!');
        }
        $group->delete();
        return back()->with('msg', 'Xóa loại đối tượng thành công.');
    }
    //xóa đối tượng trong nhóm
    public function deleteOJ(Request $request)
    {
        $id = $request['id'];
        $idGroup = $request['idGroup'];
        $success = false;
        $typeGroup = Groups::where('id', $idGroup)->first();
        if ($typeGroup->group_type_id == 1) {
            $guest = Customers::where('id', $id)->first();
            if ($guest) {
                $guest->group_id = 0;
                $guest->save();
                $success = true;
            } else {
                $success = false;
            }
        }
        if ($typeGroup->group_type_id == 2) {
            $provide = Providers::where('id', $id)->first();
            if ($provide) {
                $provide->group_id = 0;
                $provide->save();
                $success = true;
            } else {
                $success = false;
            }
        }
        if ($typeGroup->group_type_id == 3) {
            $product = Product::where('id', $id)->first();
            if ($product) {
                $product->group_id = 0;
                $product->save();
                $success = true;
            } else {
                $success = false;
            }
        }
        if ($typeGroup->group_type_id == 4) {
            $user = User::where('id', $id)->first();
            if ($user) {
                $user->group_id = 0;
                $user->save();
                $success = true;
            } else {
                $success = false;
            }
        }
        if ($success) {
            $response = ['success' => true, 'msg' => 'Xóa đối tượng trong nhóm thành công!'];
        } else {
            $response = ['success' => false, 'msg' => 'Không tìm thấy đối tượng trong nhóm!'];
        }
        return response()->json($response);
    }
    public function filterData(Request $request)
    {
        $data = $request->all();
        $filters = [];
        if ($request->ajax()) {
            $groups = $this->groups->getAllGroup($data);
            return response()->json([
                'data' => $groups,
                'filters' => $filters,
            ]);
        }
        return false;
    }


    // API vũ viết
    /**
     * API: Danh sách Loại nhóm (Dropdown)
     */
    public function types(): JsonResponse
    {
        // Lấy từ bảng group_types
        $types = DB::table('group_types')->select('id', 'group_name')->get();
        return response()->json(['success' => true, 'data' => $types]);
    }

    /**
     * API: Danh sách Nhóm (List)
     */
    public function list(Request $request): JsonResponse
    {
        $query = Groups::with('type'); // Eager load loại nhóm

        // 1. Tìm kiếm chung
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('group_code', 'like', "%{$search}%")
                    ->orWhere('group_name', 'like', "%{$search}%");
            });
        }

        // 2. Lọc chi tiết
        if ($request->filled('code')) $query->where('group_code', 'like', "%{$request->code}%");
        if ($request->filled('name')) $query->where('group_name', 'like', "%{$request->name}%");
        if ($request->filled('type_id')) $query->where('group_type_id', $request->type_id);

        // 3. Sắp xếp
        if ($request->has('sort_by') && $request->has('sort_dir')) {
            $sortBy = $request->sort_by;
            if ($sortBy === 'code') $sortBy = 'group_code';
            if ($sortBy === 'name') $sortBy = 'group_name';

            $query->orderBy($sortBy, $request->sort_dir);
        } else {
            $query->orderBy('id', 'desc');
        }

        // 4. Phân trang
        $perPage = $request->get('per_page', 20);
        $groups = $query->paginate($perPage);

        // Map dữ liệu để frontend dễ dùng (lấy tên loại nhóm ra ngoài)
        $data = collect($groups->items())->map(function ($g) {
            return [
                'id' => $g->id,
                'group_code' => $g->group_code,
                'group_name' => $g->group_name,
                'description' => $g->description,
                'group_type_id' => $g->group_type_id,
                'type_name' => $g->type ? $g->type->group_name : '---'
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $groups->currentPage(),
                'last_page' => $groups->lastPage(),
                'total' => $groups->total(),
            ]
        ]);
    }

    /**
     * API: Thêm mới
     */
    public function add(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'group_type_id' => 'required|exists:group_types,id',
            'group_code' => 'required|string|max:50|unique:groups,group_code',
            'group_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        try {
            $group = Groups::create($request->all());
            return response()->json(['success' => true, 'message' => 'Tạo nhóm thành công', 'data' => $group], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Cập nhật
     */
    public function change(Request $request, $id): JsonResponse
    {
        $group = Groups::find($id);
        if (!$group) return response()->json(['success' => false, 'message' => 'Không tìm thấy nhóm'], 404);

        $validator = Validator::make($request->all(), [
            'group_type_id' => 'required|exists:group_types,id',
            'group_code' => 'required|string|max:50|unique:groups,group_code,' . $id,
            'group_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        $group->update($request->all());
        return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'data' => $group]);
    }

    /**
     * API: Xóa (Có kiểm tra ràng buộc)
     */
    public function delete($id): JsonResponse
    {
        $group = Groups::find($id);
        if (!$group) return response()->json(['success' => false, 'message' => 'Không tìm thấy nhóm'], 404);

        // Kiểm tra ràng buộc ở 4 bảng con
        $hasCustomers = DB::table('customers')->where('group_id', $id)->exists();
        $hasProviders = DB::table('providers')->where('group_id', $id)->exists();
        $hasProducts  = DB::table('products')->where('group_id', $id)->exists();
        $hasUsers     = DB::table('users')->where('group_id', $id)->exists();

        if ($hasCustomers || $hasProviders || $hasProducts || $hasUsers) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa nhóm này vì đang được sử dụng bởi Khách hàng, NCC, Hàng hóa hoặc Nhân viên.'
            ], 422);
        }

        $group->delete();
        return response()->json(['success' => true, 'message' => 'Xóa nhóm thành công']);
    }
}
