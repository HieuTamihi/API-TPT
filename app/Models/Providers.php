<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Providers extends Model
{
    use HasFactory;
    protected $table = 'providers';
    protected $fillable = [
        'id',
        'group_id',
        'provider_code',
        'provider_name',
        'contact_person',
        'address',
        'phone',
        'email',
        'tax_code',
        'note',
    ];
    public function inventoryLookups()
    {
        return $this->hasMany(InventoryLookup::class, 'provider_id', 'id');
    }
    public function getGroup()
    {
        return $this->hasOne(Groups::class, 'id', 'group_id');
    }
    public function getAllProvide($data = null)
    {
        $provides = DB::table($this->table);
        // Tìm kiếm chung
        if (!empty($data['search'])) {
            $provides->where(function ($query) use ($data) {
                $query->where('provider_code', 'like', '%' . $data['search'] . '%')
                    ->orWhere('provider_name', 'like', '%' . $data['search'] . '%')
                    ->orWhere('address', 'like', '%' . $data['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $data['search'] . '%')
                    ->orWhere('email', 'like', '%' . $data['search'] . '%')
                    ->orWhere('note', 'like', '%' . $data['search'] . '%');
            });
        }
        // Lọc theo các trường cụ thể
        $filterableFields = [
            'ma' => 'provider_code',
            'ten' => 'provider_name',
            'address' => 'address',
            'phone' => 'phone',
            'email' => 'email',
            'note' => 'note',
        ];
        foreach ($filterableFields as $key => $field) {
            if (!empty($data[$key])) {
                $provides->where($field, 'like', '%' . $data[$key] . '%');
            }
        }
        if (isset($data['sort']) && isset($data['sort'][0])) {
            $provides = $provides->orderBy($data['sort'][0], $data['sort'][1]);
        }else{
            $provides = $provides->orderBy('id','desc');
        }

        return $provides->get();
    }

    public function addProvide($data)
    {
        $result = [];
        $provides = DB::table($this->table)->where('provider_code', $data['provider_code'])
            ->orWhere('provider_name', $data['provider_name'])
            ->first();
        if ($provides) {
            $result = [
                'status' => true,
            ];
        } else {
            $dataProvide = [
                'group_id' => isset($data['category_id']) ? $data['category_id'] : 0,
                'provider_code' => $data['provider_code'],
                'provider_name' => $data['provider_name'],
                'address' => isset($data['address']) ? $data['address'] : null,
                'contact_person' => $data['contact_person'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'tax_code' => $data['tax_code'],
                'note' => $data['note'],
                'created_at' => Carbon::now()
            ];
            $provide_id =  DB::table($this->table)->insertGetId($dataProvide);
            if ($provide_id) {
                $result = [
                    'status' => false,
                    'id' => $provide_id
                ];
            }
        }
        return $result;
    }
    public function updateProvide($data, $id)
    {
        $exist = false;
        $check = DB::table($this->table)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($data) {
                $query->where('provider_code', $data['provider_code'])
                    ->orWhere('provider_name', $data['provider_name']);
            })
            ->first();

        if ($check) {
            $exist = true;
        } else {
            $dataUpdate = [
                'provider_name' => $data['provider_name'],
                'provider_code' => $data['provider_code'],
                'address' => $data['address'],
                'contact_person' => $data['contact_person'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'tax_code' => $data['tax_code'],
                'note' => $data['note'],
                'group_id' => $data['category_id'],
                'updated_at' => Carbon::now(),
            ];
            Providers::where('id', $id)->update($dataUpdate);
            $exist = false;
        }
        return $exist;
    }
}
