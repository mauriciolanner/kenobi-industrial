<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleAccess extends Model
{
    use HasFactory;
    protected $connection = 'login';
    protected $table = 'role_accesses';
    protected $fillable = [
        'role_id',
        'role_controller_id',
        'add',
        'view',
        'edit',
        'delet'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function controller()
    {
        return $this->belongsTo(RoleController::class, 'role_controller_id');
    }
}
