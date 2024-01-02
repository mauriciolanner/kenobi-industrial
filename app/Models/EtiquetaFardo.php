<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtiquetaFardo extends Model
{
    use HasFactory;

    ///etiqueta de fardos da matriz não confundir com a da sopro

    protected $fillable = [
        'OP',
        'VIA',
        'PRODUTO',
        'QTD_ETIQUETAS',
        'user_id',
        'user_name'
    ];
}
