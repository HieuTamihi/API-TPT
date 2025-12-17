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
     * Danh sách loại nhóm (dùng cho filter hoặc dropdown nếu cần)
     */
    public function types(): JsonResponse
    {
        $types = GroupTypeMain::select('id', 'group_name as name')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Danh sách nhóm đối tượng (phân trang + tìm kiếm + sắp xếp)
     */
    public function list(Request $request): JsonResponse
    {
        // Khởi tạo query
        $query = Groups::with('grouptype:id,group_name');

        // Tìm kiếm
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('group_code', 'like', "%{$search}%")
                    ->orWhere('group_name', 'like', "%{$search}%");
            });
        }

        // Lọc theo loại nhóm
        if ($request->has('type_id') && $request->type_id > 0) {
            $query->where('group_type_id', $request->type_id);
        }

        // Xử lý Sắp xếp
        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'asc');

        if ($sortBy === 'code') {
            $query->orderBy('group_code', $sortDir);
        } elseif ($sortBy === 'name') {
            $query->orderBy('group_name', $sortDir);
        } elseif ($sortBy === 'type') {
            // Join để sort nhưng không select đè lên các cột khác
            $query->join('group_types', 'groups.group_type_id', '=', 'group_types.id')
                ->orderBy('group_types.group_name', $sortDir)
                // Quan trọng: Phải select lại các cột của groups để tránh mất dữ liệu model
                ->select('groups.*');
        } else {
            $query->orderBy('groups.id', $sortDir);
        }

        // Phân trang
        $perPage = $request->get('per_page', 20);
        $groups = $query->paginate($perPage);

        // Map dữ liệu trả về
        $data = $groups->getCollection()->map(function ($group) {
            return [
                'id' => $group->id,
                'code' => $group->group_code, // Lấy thẳng tên cột trong DB cho chắc chắn
                'name' => $group->group_name, // Lấy thẳng tên cột trong DB

                // --- SỬA LỖI TẠI ĐÂY ---
                // Gọi đúng tên function trong Model là grouptype
                'type' => $group->grouptype ? $group->grouptype->group_name : 'Chưa xác định',

                'description' => $group->description,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $groups->currentPage(),
                'last_page' => $groups->lastPage(),
                'per_page' => $groups->perPage(),
                'total' => $groups->total(),
            ]
        ]);
    }

    /**
     * Lấy chi tiết 1 nhóm
     */
    public function detail($id): JsonResponse
    {
        $group = Groups::with('grouptype')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $group->id,
                'code' => $group->group_code,
                'name' => $group->group_name,
                'type_id' => $group->group_type_id,
                'type' => $group->type->group_name ?? '',
                'description' => $group->description,
            ]
        ]);
    }

    /**
     * Tạo mới nhóm
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'group_type_id' => 'required|exists:group_types,id',
            'group_code' => 'required|string|max:255|unique:groups,group_code',
            'group_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $group = Groups::create($request->only([
            'group_type_id',
            'group_code',
            'group_name',
            'description'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Tạo nhóm thành công',
            'data' => $group->load('type')
        ], 201);
    }

    /**
     * Cập nhật nhóm
     */
    public function change(Request $request, $id): JsonResponse
    {
        $group = Groups::findOrFail($id);

        $request->validate([
            'group_type_id' => 'required|exists:group_types,id',
            'group_code' => 'required|string|max:255|unique:groups,group_code,' . $id,
            'group_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $group->update($request->only([
            'group_type_id',
            'group_code',
            'group_name',
            'description'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công',
            'data' => $group->load('type')
        ]);
    }

    /**
     * Xóa nhóm
     */
    public function delete($id): JsonResponse
    {
        $group = Groups::findOrFail($id);
        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa nhóm thành công'
        ]);
    }
}
