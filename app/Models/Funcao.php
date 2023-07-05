<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Funcao extends Model
{
    use HasFactory;

    protected $connection = 'RM';
    protected $table = 'PFUNCAO';
    protected $guarded = ['ID'];
    protected $primaryKey = 'ID';
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('coligada', function (Builder $builder) {
            $builder->select(
                'CODIGO',
                'NOME',
                'DESCRICAO',
                'INATIVA',
            )->where('PFUNCAO.CODCOLIGADA', '=', auth()->user()->coligada);
        });
    }
}
