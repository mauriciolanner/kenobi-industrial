<?php

namespace App\Models;

use App\Models\FormModels\ControleVisitaFilho;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slung',
        'status',
    ];

    public function fluxo()
    {
        return $this->hasMany(TaskForm::class);
    }
}
