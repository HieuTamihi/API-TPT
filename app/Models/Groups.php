<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Groups extends Model
{
    use HasFactory;
    protected $table = 'groups';

    protected $fillable = [
        'group_type_id',
        'group_code',
        'group_name',
        'description',
    ];
    public function grouptype()
    {
        return $this->belongsTo(GrouptypeMain::class, 'group_type_id', 'id');
    }
    public function getAll()
    {
        return Groups::with('grouptype')->get();
    }

    public function getAllGroupedByType()
    {
        $groups = Groups::with('grouptype')->orderByDesc('id')->get();

        $groupedGroups = $groups->groupBy(function ($item) {
            return $item->groupType->group_name;
        });

        return $groupedGroups;
    }
    public function addGroup($data)
    {
        $existingGroup = Groups::where('group_name', $data['group_name'])
            ->where('group_type_id', $data['group_type_id'])
            ->first();

        if ($existingGroup) {
            return true;
        }
        $datagroup = [
            'group_code' => $data['group_code'],
            'group_name' => $data['group_name'],
            'group_type_id' => $data['group_type_id'],
            'description' => $data['description'],
            'created_at' => now(),
        ];

        DB::table($this->table)->insertGetId($datagroup);

        return false;
    }
    public function getDataGroup($id)
    {
        $grouptype = Groups::find($id);
        $grouptype = $grouptype->group_type_id;
        $data = [];
        if ($grouptype == 1) {
            $data = [
                'obj' => 'customers',
                'results' => Customers::where('group_id', $id)
                    ->select('customers.id as id', 'customers.customer_name as name', 'customers.customer_code as code')->get()
            ];
        } elseif ($grouptype == 2) {
            $data = [
                'obj' => 'providers',
                'results' => Providers::where('group_id', $id)
                    ->select('providers.id as id', 'providers.provider_name as name', 'providers.provider_code as code')->get()
            ];
        } elseif ($grouptype == 3) {
            $data = [
                'obj' => 'products',
                'results' => Product::where('group_id', $id)->select('products.id as id', 'products.product_name as name', 'products.product_code as code')->get()
            ];
        } elseif ($grouptype == 4) {
            $data = [
                'obj' => 'users',
                'results' => User::where('group_id', $id)
                    ->select('users.id as id', 'users.name as name', 'users.employee_code as code')->get()
            ];
        }
        return $data;
    }
    public function updateGroup($data, $id)
    {
        return DB::table($this->table)->where('id', $id)->update($data);
    }
    public function updateDataGroup($data)
    {
        DB::table($data['data_obj'])
            ->whereIn('id', $data['dataupdate'])
            ->update(['group_id' => $data['group_id']]);
        $grouptype = Groups::find($data['group_id']);
        $grouptype = $grouptype->group_type_id;
        $dataRs = [];
        if ($grouptype == 1) {
            $dataRs = [
                'obj' => 'customers',
                'results' => Customers::whereIn('id', $data['dataupdate'])
                    ->select('customers.id as id', 'customers.customer_name as name')->get()
            ];
        } elseif ($grouptype == 2) {
            $dataRs = [
                'obj' => 'providers',
                'results' => Providers::whereIn('id', $data['dataupdate'])
                    ->select('providers.id as id', 'providers.provider_name as name')->get()
            ];
        } elseif ($grouptype == 3) {
            $dataRs = [
                'obj' => 'products',
                'results' => Product::whereIn('id', $data['dataupdate'])
                    ->select('products.id as id', 'products.product_name as name')->get()
            ];
        } elseif ($grouptype == 4) {
            $dataRs = [
                'obj' => 'users',
                'results' => User::whereIn('id', $data['dataupdate'])
                    ->select('users.id as id', 'users.name as name')->get()
            ];
        }
        return $dataRs;
    }
    public function dataObj($idGr)
    {
        $data = [];
        if ($idGr == 1) {
            $data = [
                'obj' => 'customers',
                'results' => Customers::where('group_id', 0)->select('customers.id as id', 'customers.customer_name as name')->get()
            ];
        } elseif ($idGr == 2) {
            $data = [
                'obj' => 'providers',
                'results' => Providers::where('group_id', 0)->select('providers.id as id', 'providers.provider_name as name')->get()
            ];
        } elseif ($idGr == 3) {
            $data = [
                'obj' => 'products',
                'results' => Product::where('group_id', 0)->select('products.id as id', 'products.product_name as name')->get()
            ];
        } elseif ($idGr == 4) {
            $data = [
                'obj' => 'users',
                'results' => User::where('group_id', 0)->select('users.id as id', 'users.name as name')->get()
            ];
        }
        return $data;
    }
    public function getAllGroup($data = null)
    {
        $groups = DB::table($this->table)->leftJoin('group_types', 'group_types.id', 'groups.group_type_id')
            ->select('groups.*', 'group_types.group_name as nameGroup');
        if (!empty($data)) { // Kiểm tra $data có dữ liệu
            $groups->where(function ($query) use ($data) {
                $query->where('groups.group_code', 'like', '%' . $data['search'] . '%')
                    ->orWhere('groups.group_name', 'like', '%' . $data['search'] . '%');
            });
        }
        if (isset($data['sort']) && isset($data['sort'][0])) {
            $groups = $groups->orderBy($data['sort'][0], $data['sort'][1]);
        }
        return $groups->get();
    }
}
