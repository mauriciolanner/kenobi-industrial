<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FormModels\FormControleVisita;

class Approver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ordem',
        'type',
        'user_id',
        'form_id',
        'status',
        'group',
        'create_user_id',
        'task_forms_id',
        'form_number'
    ];

    public function userApprover()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userCreate()
    {
        return $this->belongsTo(User::class,  'create_user_id');
    }

    public function form()
    {
        return $this->belongsTo(FormControleVisita::class);
    }

    public function groupDetail()
    {
        return $this->belongsTo(Role::class, 'group', 'id');
    }
}
