<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'              => 'required|string|unique:warehouse_transfers,code|max:255',
            'from_warehouse_id' => 'required|integer|exists:warehouses,id',
            'to_warehouse_id'   => 'required|integer|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date'     => 'nullable|date',
            'status'            => 'integer|in:0,1',
            'note'              => 'nullable|string',
            // Nếu bạn muốn truyền user_id từ client (thường thì lấy từ Auth::id() ở controller)
            // 'user_id'        => 'required|exists:users,id', 
        ];
    }
    
    public function messages()
    {
        return [
            'to_warehouse_id.different' => 'Kho đích không được trùng với kho nguồn.',
            'code.unique' => 'Mã phiếu chuyển kho đã tồn tại.',
        ];
    }
}