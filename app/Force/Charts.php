<?php

namespace App\Force;

use App\Models\Pedido;
use Carbon\Carbon;

class Charts
{
    //
    public function chartPedidosAno($cliente)
    {
        $chart = (object)[
            'chartOptions' => [
                'chart' => [
                    'id' => 'vuechart-pedidos'
                ],
                'xaxis' => [
                    'categories' => [
                        $this->mes(Carbon::now()->subMonths(12)->format('m')),
                        $this->mes(Carbon::now()->subMonths(11)->format('m')),
                        $this->mes(Carbon::now()->subMonths(10)->format('m')),
                        $this->mes(Carbon::now()->subMonths(9)->format('m')),
                        $this->mes(Carbon::now()->subMonths(8)->format('m')),
                        $this->mes(Carbon::now()->subMonths(7)->format('m')),
                        $this->mes(Carbon::now()->subMonths(6)->format('m')),
                        $this->mes(Carbon::now()->subMonths(5)->format('m')),
                        $this->mes(Carbon::now()->subMonths(4)->format('m')),
                        $this->mes(Carbon::now()->subMonths(3)->format('m')),
                        $this->mes(Carbon::now()->subMonths(2)->format('m')),
                        $this->mes(Carbon::now()->subMonths(1)->format('m'))
                    ]
                ]
            ],
            'series' => [[
                'name' => "Pedidos neste mês",
                'data' => [
                    $this->pedidosMes($cliente, Carbon::now()->subMonths(12)->format('m'), Carbon::now()->format('Y')),
                    $this->pedidosMes($cliente, Carbon::now()->subMonths(11)->format('m'), Carbon::now()->format('Y')),
                    $this->pedidosMes($cliente, Carbon::now()->subMonths(10)->format('m'), Carbon::now()->format('Y')),
                    $this->pedidosMes($cliente, Carbon::now()->subMonths(9)->format('m'), Carbon::now()->format('Y')),
                    $this->pedidosMes($cliente, Carbon::now()->subMonths(8)->format('m'), Carbon::now()->format('Y')),
                    $this->pedidosMes($cliente, Carbon::now()->subMonths(7)->format('m'), Carbon::now()->format('Y')),
                    $this->pedidosMes($cliente, Carbon::now()->subMonths(6)->format('m'), Carbon::now()->format('Y')),
                    $this->pedidosMes($cliente, Carbon::now()->subMonths(5)->format('m'), Carbon::now()->format('Y')),
                    $this->pedidosMes($cliente, Carbon::now()->subMonths(4)->format('m'), Carbon::now()->format('Y')),
                    $this->pedidosMes($cliente, Carbon::now()->subMonths(3)->format('m'), Carbon::now()->format('Y')),
                    $this->pedidosMes($cliente, Carbon::now()->subMonths(2)->format('m'), Carbon::now()->format('Y')),
                    $this->pedidosMes($cliente, Carbon::now()->subMonths(1)->format('m'), Carbon::now()->format('Y'))
                ]
            ]]
        ];

        return $chart;
    }

    static function pedidosMes($cliente, $mes, $ano)
    {
        return count(Pedido::where('C5_CLIENTE', $cliente)
            ->whereBetween(
                'C5_EMISSAO',
                [
                    $ano . $mes . '01',
                    $ano . $mes . '31',
                ]
            )->get());
    }

    static function mes($mes)
    {
        if ($mes == '01')
            return 'Jan';
        if ($mes == '02')
            return 'Fev';
        if ($mes == '03')
            return 'Mar';
        if ($mes == '04')
            return 'Abr';
        if ($mes == '05')
            return 'Mai';
        if ($mes == '06')
            return 'Jun';
        if ($mes == '07')
            return 'Jul';
        if ($mes == '08')
            return 'Ago';
        if ($mes == '09')
            return 'Set';
        if ($mes == '10')
            return 'Out';
        if ($mes == '11')
            return 'Nov';
        if ($mes == "12")
            return 'Dez';
    }
}
