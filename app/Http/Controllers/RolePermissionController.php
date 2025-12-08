<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    // Danh sách roles và permissions
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $user = Auth::user();

        return view('setup.roles.roles-permissions', compact('roles', 'permissions'));
    }

    // Tạo mới vai trò
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
        ]);

        $role = Role::create(['name' => $request->name]);
        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->back()->with('success', 'Vai trò đã được tạo!');
    }

    // Cập nhật vai trò
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'array',
        ]);

        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }
        return redirect()->back()->with('success', 'Vai trò đã được cập nhật!');
    }


    // Xóa vai trò
    public function destroyRole($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->back()->with('success', 'Vai trò đã được xóa!');
    }

    // Tạo mới quyền
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'Quyền đã được tạo!');
    }

    // Xóa quyền
    public function destroyPermission($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->back()->with('success', 'Quyền đã được xóa!');
    }
}
