<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Bỏ qua check unique cho chính ID đang sửa
            'code'              => 'sometimes|string|max:255|unique:warehouse_transfers,code,' . $this->route('warehouse_transfer'), 
            'from_warehouse_id' => 'sometimes|integer|exists:warehouses,id',
            'to_warehouse_id'   => 'sometimes|integer|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date'     => 'nullable|date',
            'status'            => 'integer|in:0,1',
            'note'              => 'nullable|string',
        ];
    }
}