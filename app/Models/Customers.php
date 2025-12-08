<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customers extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_id',
        'customer_code',
        'customer_name',
        'contact_person',
        'address',
        'phone',
        'email',
        'tax_code',
        'note',
    ];
    protected $table = 'customers';
    public function getAllGuest($data = null)
    {
        $guests = DB::table($this->table);

        if (!empty($data)) { // Kiểm tra $data có dữ liệu
            $guests->where(function ($query) use ($data) {
                $query->where('customer_code', 'like', '%' . $data['search'] . '%')
                    ->orWhere('customer_name', 'like', '%' . $data['search'] . '%')
                    ->orWhere('address', 'like', '%' . $data['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $data['search'] . '%')
                    ->orWhere('email', 'like', '%' . $data['search'] . '%')
                    ->orWhere('note', 'like', '%' . $data['search'] . '%');
            });
        }
        $filterableFields = [
            'ma' => 'customer_code',
            'ten' => 'customer_name',
            'address' => 'address',
            'phone' => 'phone',
            'email' => 'email',
            'note' => 'note',
        ];
        foreach ($filterableFields as $key => $field) {
            if (!empty($data[$key])) {
                $guests->where($field, 'like', '%' . $data[$key] . '%');
            }
        }
        if (isset($data['sort']) && isset($data['sort'][0])) {
            $guests = $guests->orderBy($data['sort'][0], $data['sort'][1]);
        }else{
            $guests = $guests->orderBy('id','desc');
        }
        return $guests->get();
    }
    public function addGuest($data)
    {
        $exist = false;
        $guests = DB::table($this->table)
            ->where(function ($query) use ($data) {
                $query->where('customer_code', $data['customer_code'])
                    ->orWhere('customer_name', $data['customer_name']);
            })
            ->first();
        if ($guests) {
            $exist = true;
        } else {
            $dataguest = [
                'customer_code' => $data['customer_code'],
                'customer_name' => $data['customer_name'],
                'address' => $data['address'],
                'contact_person' => $data['contact_person'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'tax_code' => $data['tax_code'],
                'note' => $data['note'],
                'group_id' => isset($data['grouptype_id']) ? $data['grouptype_id'] : 0,
                'created_at' => now()
            ];
            DB::table($this->table)->insertGetId($dataguest);
        }
        return $exist;
    }
    public function updateCustomer($data, $id)
    {
        return DB::table($this->table)->where('id', $id)->update($data);
    }
}
