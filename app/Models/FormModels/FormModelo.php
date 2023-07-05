<?php

namespace App\Models\FormModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Form;
use App\Models\Approver;
use App\Models\FormNumber;
use App\Models\LogForm;

class FormModelo extends Model
{
    use HasFactory;

    protected $fillable = [
        "usuarioAbertura",
        "dataAbertura",
        'solicitante',
        'chapaSolicitante',
        'exemploData',
        'exemploTexto',
        //camposPadrao
        'form_number',
        'create_user_id',
        'form_id',
        'status',
    ];

    public function childs()
    {
        return $this->hasMany(DefaltChild::class, 'form_number', 'form_number');
    }

    public function formulario()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function formularioNumero()
    {
        return $this->belongsTo(FormNumber::class, 'form_number', 'id');
    }

    public function aprovacao()
    {
        return $this->hasMany(Approver::class, 'form_number', 'form_number');
    }

    public function historico()
    {
        return $this->hasMany(LogForm::class, 'form_number', 'form_number');
    }
}
