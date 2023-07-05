<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Secao extends Model
{
    use HasFactory;

    protected $connection = 'RM';
    protected $table = 'PSECAO';
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('coligada', function (Builder $builder) {
            $builder->select('CODCOLIGADA', 'CODIGO', 'DESCRICAO')->where('SECAODESATIVADA', '0');
        });
    }

    public function coligada()
    {
        return  $this->belongsTo(Coligada::class, 'CODCOLIGADA', 'CODCOLIGADA');
    }
}
