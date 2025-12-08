<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        //
    }
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true, 'id' => $id]);
    }

    public function markAllAsRead($type, Request $request)
    {
        // Lấy mảng các ID từ yêu cầu
        $ids = $request->input('ids', []);

        // Kiểm tra nếu có ID
        if (!empty($ids)) {
            // Xử lý theo loại (type) để xác định bảng hoặc quan hệ
            if ($type == 'receiving') {
                // Đánh dấu các thông báo theo receiving_id đã đọc
                auth()->user()->notifications()->whereIn('data->receiving_id', $ids)->update(['read_at' => now()]);
            } elseif ($type == 'inventoryLookup') {
                // Đánh dấu các thông báo theo inventoryLookup_id đã đọc
                auth()->user()->notifications()->whereIn('data->inventoryLookup_id', $ids)->update(['read_at' => now()]);
            }
        }

        // Trả về phản hồi JSON khi hoàn tất
        return response()->json(['success' => true]);
    }
}
