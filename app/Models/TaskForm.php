<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TaskForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ordem',
        'type',
        'form_id',
        'group',
        'status',
        'user_id'
    ];

    protected static function booted()
    {
        static::addGlobalScope('filial', function (Builder $builder) {
            $builder->orderBy('ordem', 'asc');
        });
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
