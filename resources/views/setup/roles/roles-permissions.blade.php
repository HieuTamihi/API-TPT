@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center">Quản Lý Role & Permission</h1>

        {{-- Danh sách roles --}}
        <div class="card my-4">
            <div class="card-header">
                <h4>Danh Sách Vai Trò (Roles)</h4>
                <button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#addRoleModal">Thêm Vai
                    Trò</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên Vai Trò</th>
                            <th>Quyền (Permissions)</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    @foreach ($role->permissions as $permission)
                                        <span class="badge badge-success">{{ $permission->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-toggle="modal"
                                        data-target="#editRoleModal-{{ $role->id }}">Sửa</button>
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            @include('setup.roles.edit-role-modal', [
                                'role' => $role,
                                'permissions' => $permissions,
                            ])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Danh sách permissions --}}
        <div class="card my-4">
            <div class="card-header">
                <h4>Danh Sách Quyền (Permissions)</h4>
                <button class="btn btn-primary btn-sm float-right" data-toggle="modal"
                    data-target="#addPermissionModal">Thêm Quyền</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên Quyền</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $permission)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>
                                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('setup.roles.add-role-modal')
    @include('setup.roles.add-permission-modal')
@endsection
