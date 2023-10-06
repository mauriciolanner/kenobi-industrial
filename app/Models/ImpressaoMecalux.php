<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImpressaoMecalux extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'CODIGO_APONTAMENTO',
        'CODIGO_APONTAMENTO',
        'APONTAMENTO_MES',
        'ID_INTEGRACAO_MES',
        'DtMov',
        'QUANTIDADE',
        'PRODUTO',
        'RECEITA',
        'OP',
        'ARMAZEM',
        'ErrDescription',
        'IDPCFACTORY',
        'RECURSO',
        'IMPRESSO',
        'ESTORNO',
        'created_at'
    ];
}
