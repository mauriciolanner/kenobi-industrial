<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleController extends Model
{
    use HasFactory;
    protected $connection = 'login';
    protected $fillable = [
        'name',
        'slung'
    ];
}
