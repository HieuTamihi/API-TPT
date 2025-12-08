<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Rules\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'group_id',
        'employee_code',
        'role',
        'address',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getAllUsers($data = null)
    {
        $users = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as rolename');
        // Tìm kiếm chung
        if (!empty($data['search'])) {
            $users->where(function ($query) use ($data) {
                $query->where('employee_code', 'like', '%' . $data['search'] . '%')
                    ->orWhere('users.name', 'like', '%' . $data['search'] . '%')
                    ->orWhere('address', 'like', '%' . $data['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $data['search'] . '%')
                    ->orWhere('email', 'like', '%' . $data['search'] . '%');
            });
        }
        // Lọc theo các trường cụ thể
        $filterableFields = [
            'ma' => 'employee_code',
            'ten' => 'users.name',
            'address' => 'address',
            'phone' => 'phone',
            'email' => 'email',
            'note' => 'note',
        ];
        foreach ($filterableFields as $key => $field) {
            if (!empty($data[$key])) {
                $users->where($field, 'like', '%' . $data[$key] . '%');
            }
        }
        if (isset($data['roles']) && is_array($data['roles']) && !empty($data['roles'])) {
            $users = $users->whereIn('roles.id', $data['roles']);
        }
        if (isset($data['sort']) && isset($data['sort'][0])) {
            $users = $users->orderBy($data['sort'][0], $data['sort'][1]);
        }
        return $users->get();
    }
    //relationship warehouseTransfer
    public function warehouseTransfer()
    {
        return $this->hasMany(WarehouseTransfer::class);
    }
}
