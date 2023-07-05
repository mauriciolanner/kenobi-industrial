<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Horario extends Model
{
    use HasFactory;

    protected $connection = 'RM';
    protected $table = 'AHORARIO';
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('coligada', function (Builder $builder) {
            $builder->select(
                'CODIGO',
                'DESCRICAO'
            )->where('CODCOLIGADA', auth()->user()->coligada);
        });
    }
}
