<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CentroCusto extends Model
{
    protected $connection = 'RM';
    protected $table = 'PCCUSTO';
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('coligada', function (Builder $builder) {
            $builder->select(
                'CODCCUSTO',
                'NOME'
            )->where('ATIVO', 'T')
                ->where('CODCOLIGADA', auth()->user()->coligada);
        });
    }
}
