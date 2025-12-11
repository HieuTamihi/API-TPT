<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Bỏ qua kiểm tra CSRF cho tất cả các đường dẫn bắt đầu bằng "imports/"
        'imports/*',
        'exports/*',
        'receivings/*',

        // Nếu bạn có các route khác cần test Postman trong web.php, thêm vào dưới đây:
        // 'products/*',
        // 'customers/*',
    ];
}