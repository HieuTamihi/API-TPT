@extends('layouts.app')

@section('content')
<style>
    html, body {
        height: 100%;
        margin: 0;
        overflow: hidden; /* Ẩn thanh cuộn */
    }
    .container-fullscreen {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh; /* Chiếm toàn bộ chiều cao màn hình */
    }
    .card {
        width: 100%;
        max-width: 500px; /* Giới hạn chiều rộng của form */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ */
        border-radius: 10px; /* Bo góc */
    }
</style>

<div class="container-fullscreen">
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0" style="font-weight: bold">Chỉnh sửa hồ sơ</h3>
        </div>

        <div class="card-body">
            {{-- Thông báo thành công --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Thông báo lỗi --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.update.profile') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Họ và tên --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Họ và tên</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                        value="{{ old('name', auth()->user()->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                        value="{{ old('email', auth()->user()->email) }}" readonly>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mật khẩu mới --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu mới (không bắt buộc)</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Xác nhận mật khẩu --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>

                {{-- Nút cập nhật --}}
                <button type="submit" class="btn btn-primary w-100">Cập nhật hồ sơ</button>
            </form>
        </div>
    </div>
</div>
@endsection
