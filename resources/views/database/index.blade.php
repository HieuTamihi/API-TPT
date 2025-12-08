@extends('layouts.app')

@section('title', 'Quản lý Database')

@section('content')
    <div class="database-wrapper">
        <div class="database-container">
            {{-- Header --}}
            <div class="database-header">
                <div class="header-icon">
                    <i class="fas fa-database"></i>
                </div>
                <h1>Quản lý Database</h1>
                <p class="header-subtitle">Sao lưu và khôi phục dữ liệu hệ thống</p>
            </div>

            {{-- Database Info --}}
            <div class="db-info-card">
                <div class="info-item">
                    <span class="info-label">Database:</span>
                    <span class="info-value">{{ $dbName }}</span>
                </div>
            </div>

            {{-- Action Cards --}}
            <div class="action-cards">
                {{-- Export Card --}}
                <div class="action-card export-card">
                    <div class="card-icon">
                        <i class="fas fa-cloud-download-alt"></i>
                    </div>
                    <h3>Export Database</h3>
                    <p>Tải xuống bản sao lưu (backup) của database hiện tại dưới dạng file SQL.</p>
                    <form action="{{ route('database.export') }}" method="POST">
                        @csrf
                        <input type="hidden" name="download" value="1">
                        <button type="submit" class="btn-action btn-export">
                            <i class="fas fa-download"></i>
                            <span>Export & Download</span>
                        </button>
                    </form>
                </div>

                {{-- Import Card --}}
                <div class="action-card import-card">
                    <div class="card-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h3>Import Database</h3>
                    <p>Khôi phục dữ liệu từ file SQL backup. Dữ liệu hiện tại sẽ bị ghi đè.</p>
                    <form action="{{ route('database.import') }}" method="POST" enctype="multipart/form-data"
                        id="importForm">
                        @csrf
                        <div class="file-upload-wrapper">
                            <input type="file" id="sql_file" name="sql_file" accept=".sql,.txt" required>
                            <label for="sql_file" class="file-upload-label">
                                <i class="fas fa-file-upload"></i>
                                <span id="file-name">Chọn file SQL...</span>
                            </label>
                        </div>
                        @error('sql_file')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        <button type="button" class="btn-action btn-import" onclick="confirmImport()">
                            <i class="fas fa-upload"></i>
                            <span>Import Database</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Warning Note --}}
            <div class="warning-note">
                <i class="fas fa-exclamation-circle"></i>
                <span><strong>Lưu ý:</strong> Hãy export backup trước khi thực hiện import để tránh mất dữ liệu!</span>
            </div>
        </div>
    </div>

    <script>
        // Hiển thị tên file đã chọn
        document.getElementById('sql_file').addEventListener('change', function () {
            var fileName = this.files[0] ? this.files[0].name : 'Chọn file SQL...';
            document.getElementById('file-name').textContent = fileName;

            if (this.files[0]) {
                this.closest('.file-upload-wrapper').classList.add('has-file');
            } else {
                this.closest('.file-upload-wrapper').classList.remove('has-file');
            }
        });

        // Xác nhận import
        function confirmImport() {
            var fileInput = document.getElementById('sql_file');
            if (!fileInput.files[0]) {
                Swal.fire({
                    title: 'Chưa chọn file!',
                    text: 'Vui lòng chọn file SQL để import.',
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Xác nhận Import?',
                html: '<div style="text-align: left; padding: 10px;">' +
                    '<p style="margin-bottom: 10px;"><strong>⚠️ Cảnh báo:</strong></p>' +
                    '<ul style="margin-left: 20px;">' +
                    '<li>Toàn bộ dữ liệu hiện tại sẽ bị ghi đè</li>' +
                    '<li>Thao tác không thể hoàn tác</li>' +
                    '</ul>' +
                    '<p style="margin-top: 15px;">Bạn có chắc chắn muốn tiếp tục?</p>' +
                    '</div>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: '<i class="fas fa-check"></i> Có, Import ngay!',
                cancelButtonText: '<i class="fas fa-times"></i> Hủy bỏ',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Đang import...',
                        html: 'Vui lòng đợi trong giây lát',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('importForm').submit();
                }
            });
        }
    </script>

    <style>
        .database-wrapper {
            min-height: calc(100vh - 120px);
            padding: 40px 20px;
            margin-top: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .database-container {
            max-width: 900px;
            width: 100%;
        }

        .database-header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            backdrop-filter: blur(10px);
        }

        .header-icon i {
            font-size: 36px;
            color: white;
        }

        .database-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        .db-info-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 15px 25px;
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
        }

        .info-label {
            font-size: 14px;
            opacity: 0.9;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.25);
            padding: 5px 15px;
            border-radius: 20px;
        }

        .action-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .action-cards {
                grid-template-columns: 1fr;
            }
        }

        .action-card {
            background: white;
            border-radius: 20px;
            padding: 35px 30px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .card-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .export-card .card-icon {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .import-card .card-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .card-icon i {
            font-size: 28px;
            color: white;
        }

        .action-card h3 {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 12px;
        }

        .action-card p {
            font-size: 14px;
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 25px;
            min-height: 44px;
        }

        .btn-action {
            width: 100%;
            padding: 14px 25px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .btn-export {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .btn-export:hover {
            box-shadow: 0 8px 20px rgba(17, 153, 142, 0.4);
            transform: translateY(-2px);
        }

        .btn-import {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .btn-import:hover {
            box-shadow: 0 8px 20px rgba(245, 87, 108, 0.4);
            transform: translateY(-2px);
        }

        .file-upload-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .file-upload-wrapper input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 20px;
            border: 2px dashed #ddd;
            border-radius: 12px;
            background: #f8f9fa;
            color: #7f8c8d;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-wrapper:hover .file-upload-label,
        .file-upload-wrapper.has-file .file-upload-label {
            border-color: #f5576c;
            background: #fff5f6;
            color: #f5576c;
        }

        .file-upload-label i {
            font-size: 20px;
        }

        .error-message {
            color: #e74c3c;
            font-size: 13px;
            margin-bottom: 15px;
            text-align: left;
        }

        .warning-note {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            font-size: 14px;
        }

        .warning-note i {
            font-size: 20px;
            color: #ffeaa7;
        }
    </style>
@endsection