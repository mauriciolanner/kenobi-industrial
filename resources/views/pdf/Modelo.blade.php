<!DOCTYPE html>
<html lang="en">
<title>Bomix</title>
<style>
    * {
        font-family: arial, sans-serif;
        font-size: 10px;
    }

    h1 {
        font-size: 15px !important;
        margin-top: 1px;
    }

    table {
        width: 100%;
        margin-bottom: 10px;
    }

    .limpo {
        border: 0px solid #000000;
        text-align: left;
        padding: 8px;
    }

    hr.dashed {
        border-top: 1px solid rgb(44, 44, 44);
        margin-top: 32px !important;
    }

    .table-itens {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 10px;
    }

    .table-itens td,
    th {
        border: 1px solid #000000;
        text-align: left;
        padding: 8px;
    }

    .table-itens tr:nth-child(even) {
        background-color: #dddddd;
    }

    @page {
        size: landscape;
        display: none;
        margin: 5mm;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

</head>

<body class="" onload="window.print() ">
    <table>
        <tr class="limpo">
            <th class="limpo">
                <img src="{{ $logo }}" style="width: 150px;" alt="0">
                <h1>Bomix Base - Formulário modeloF</h1>
            </th>
            <th class="limpo" style="text-align: right;">
                {{ QrCode::size(100)->generate(asset('/form/Modelo/detalhes/' . $data->form_number)) }}
            </th>
        </tr>
    </table>

    <p>
        <b>Fomulário numero:</b> {{ $data->form_number }}<br>
        <b>Aberto em:</b>
        {{ sprintf(
            '%s/%s/%02d',
            substr($data->dataAbertura, -2),
            substr($data->dataAbertura, -5, -3),
            substr($data->dataAbertura, -11, -5),
        ) }}
        <b>Aberto por:</b> {{ $data->usuarioAbertura }}<br>
    </p>





</body>

</html>
