<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Auditable;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes , Auditable;
    

    protected $fillable = [
        'name', 'email', 'phone', 'national_id', 'password', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];
}

    public function getRoleDisplayNameAttribute(): string
    {
        $map = [
            'admin' => 'مدير النظام',
            'warehouse_manager' => 'مدير المخازن',
            'store_keeper' => 'أمين المخزن',
            'user' => 'مستخدم',
        ];
        $role = $this->roles->first();
        return $role ? ($map[$role->name] ?? $role->name) : 'بدون دور';
    }
}