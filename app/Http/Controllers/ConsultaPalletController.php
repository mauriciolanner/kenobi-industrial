<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class ConsultaPalletController extends Controller
{
    //
    public function index(Request $request)
    {
        return Inertia::render(
            'NotLoginPages/ConsultaOP',
            []
        );
    }
}
