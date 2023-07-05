<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Funcionario extends Model
{
    use HasFactory;

    protected $connection = 'RM';
    protected $table = 'PFUNC';
    protected $guarded = ['ID'];
    protected $primaryKey = 'ID';
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('coligada', function (Builder $builder) {
            $builder->select(
                'DATAADMISSAO',
                'CHAPA',
                'PCODSITUACAO.DESCRICAO as SITUACAO',
                'CODPESSOA',
                'CODSITUACAO',
                'DATADEMISSAO',
                'CODSECAO',
                'CODFUNCAO',
                'NOME',
                'PISPASEP',
                'CODHORARIO',
                'PFUNC.CODCOLIGADA'
            )->join('PCODSITUACAO', 'PFUNC.CODSITUACAO', '=', 'PCODSITUACAO.CODINTERNO');
        });
    }

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'CODPESSOA', 'CODIGO');
    }

    public function coligada()
    {
        return $this->belongsTo(Coligada::class, 'CODCOLIGADA', 'CODCOLIGADA');
    }

    public function funcao()
    {
        return $this->belongsTo(Funcao::class, 'CODFUNCAO', 'CODIGO')
            ->where('CODCOLIGADA', auth()->user()->coligada);
    }

    public function secao()
    {
        return $this->belongsTo(Secao::class, 'CODSECAO', 'CODIGO')
            ->where('CODCOLIGADA', auth()->user()->coligada);
    }

    public function ocorrenciasFalta()
    {
        return $this->hasMany(OcorrenciaSemTrato::class, 'CHAPA', 'CHAPA')
            ->where('FALTA', '<>', '0')->where('CODCOLIGADA', auth()->user()->coligada);
    }

    public function ocorrenciasAtraso()
    {
        return $this->hasMany(OcorrenciaSemTrato::class, 'CHAPA', 'CHAPA')
            ->where('ATRASO', '<>', '0')->where('CODCOLIGADA', auth()->user()->coligada);
    }

    public function ocorrenciasHoraTrab()
    {
        return $this->hasMany(OcorrenciaSemTrato::class, 'CHAPA', 'CHAPA')
            ->where('HTRAB', '<>', '0')->where('CODCOLIGADA', auth()->user()->coligada);
    }

    public function espelhoPonto()
    {
        return $this->hasMany(RegistroPonto::class, 'CHAPA', 'CHAPA')
            ->where('CODCOLIGADA', auth()->user()->coligada);
    }

    public function ferias()
    {
        return $this->hasMany(Ferias::class, 'CHAPA', 'CHAPA')
            ->where('CODCOLIGADA', auth()->user()->coligada);
    }

    public function atestado()
    {
        return $this->hasMany(Atestado::class, 'CHAPA', 'CHAPA')
            ->where('CODCOLIGADA', auth()->user()->coligada);
    }

    public function previdencia()
    {
        return $this->hasMany(Previdencia::class, 'CHAPA', 'CHAPA')
            ->where('CODCOLIGADA', auth()->user()->coligada);
    }

    public function suspensao()
    {
        return $this->hasMany(Suspensao::class, 'CHAPA', 'CHAPA')
            ->where('CODCOLIGADA', auth()->user()->coligada);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'CODHORARIO', 'CODIGO')
            ->where('CODCOLIGADA', auth()->user()->coligada);
    }

    public function beneficios()
    {
        return $this->belongsTo(Beneficos::class, 'CHAPA', 'CHAPA');
    }

    public function centroCusto()
    {
        return $this->belongsTo(RateioFixo::class, 'CHAPA', 'CHAPA');
    }
}
