<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormNumber extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'form_id',
        'form_opem_id',
        'user_id',
        'status_form',
        'task',
        'status',
        'atual_user',
        'atual_group',
        'atual_task',
    ];

    protected $dates = ['deleted_at'];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function group()
    {
        return $this->belongsTo(Role::class, 'atual_group', 'id');
    }

    public function atualAprouver()
    {
        return $this->belongsTo(User::class, 'atual_user');
    }

    public function tasks()
    {
        return $this->hasMany(Approver::class, 'form_number');
    }

    public function atualTask()
    {
        return $this->belongsTo(Approver::class, 'atual_task');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
