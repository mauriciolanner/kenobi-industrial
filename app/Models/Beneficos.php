<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Beneficos extends Model
{
    use HasFactory;

    protected $connection = 'RM';
    protected $table = 'PFCOMPL';
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('coligada', function (Builder $builder) {
            $builder->select(
                'PFCOMPL.CHAPA',
                'PFCOMPL.ALM1',
                'PFCOMPL.ALM',
                'PFCOMPL.ALM2',
                'PLODONTO.DESCRICAO as PLODONTOLOGICO',
                'PLSAUDE.DESCRICAO as PLSAUDE',
                'ALM.DESCRICAO as ALIMENTACAO',
            )
                ->join(DB::raw('GCONSIST as PLODONTO'), 'PFCOMPL.PLODONTO', '=', 'PLODONTO.CODINTERNO')
                ->join(DB::raw('GCONSIST as PLSAUDE'), 'PFCOMPL.PLSAUDE', '=', 'PLSAUDE.CODINTERNO')
                ->join(DB::raw('GCONSIST as ALM'), 'PFCOMPL.ALM', '=', 'ALM.CODINTERNO')
                ->where('PLODONTO.CODTABELA', 'PLODONTO')
                ->where('PLSAUDE.CODTABELA', 'PLSAUDE')
                ->where('ALM.CODTABELA', '01');
        });
    }
}
