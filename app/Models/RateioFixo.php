<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RateioFixo extends Model
{
    use HasFactory;

    protected $connection = 'RM';
    protected $table = 'PFRATEIOFIXO';
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('coligada', function (Builder $builder) {
            $builder->select(
                'PFRATEIOFIXO.CHAPA',
                'PFRATEIOFIXO.VALOR',
                'PCCUSTO.NOME',
                'PCCUSTO.CODCCUSTO',
            )->join('PCCUSTO', 'PFRATEIOFIXO.CODCCUSTO', '=', 'PCCUSTO.CODCCUSTO')
                ->where('PCCUSTO.CODCOLIGADA', auth()->user()->coligada)->where('PCCUSTO.CODCOLIGADA', auth()->user()->coligada);
        });
    }

    public function coligada()
    {
        return  $this->belongsTo(Coligada::class, 'CODCOLIGADA', 'CODCOLIGADA');
    }
}
