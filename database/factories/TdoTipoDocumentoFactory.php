<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TdoTipoDocumento;

class TdoTipoDocumentoFactory extends Factory
{
    protected $model = TdoTipoDocumento::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $tdo_tipo_documentoArray = [
            'Nota Fiscal',
            'Faturas e guias',
            'Alvará de funcionamento',
            'Licenças de funcionamento',
            'Recibos e comprovantes de pagamentos',
            'Registros contábeis e fiscais',
            'Controle de ponto',
            'Folhas de pagamento'
        ];
        $tdo_tipo_documento = $tdo_tipo_documentoArray[rand(0, count($tdo_tipo_documentoArray) - 1)];
        return [
            'tdo_nom_tipo_documento' => $tdo_tipo_documento
        ];
    }
}
