<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Coligada extends Model
{
    protected $connection = 'RM';
    protected $table = 'GCOLIGADA';
    use HasFactory;
    protected static function booted()
    {
        static::addGlobalScope('coligada', function (Builder $builder) {
            $builder->select(
                'NOMEFANTASIA',
                'CODCOLIGADA',
                'CGC',
                'NOME'
            );
        });
    }
}
