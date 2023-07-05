<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Approver;
use App\Models\AllForm;
use App\Models\FormNumber;
use Illuminate\Http\Request;
use App\Models\PushNotification;
use Inertia\Inertia;

class FormController extends Controller
{
    //
    public function index()
    {
        return Inertia::render(
            'FormFluig/IndexList',
            []
        );
    }

    public function show($formNUmber)
    {
        $statusNotification = PushNotification::where('form_number', $formNUmber)->where('user_id', Auth()->user()->id)->get();
        foreach ($statusNotification as $destry) {
            PushNotification::destroy($destry->id);
        }

        $formNum = FormNumber::with('form')->where('id', $formNUmber)->first();
        return redirect()->route('form.' . $formNum->form->slung . '.ver', $formNUmber);
    }

    public function dashboard()
    {
        return Inertia::render(
            'FormFluig/Dashboard',
            []
        );
    }

    public function allForms(Request $request)
    {
        $busca = json_decode($request->buscar);
        if (session('borderoEdit')) {
            //administrador ve tudo
            $allForms = FormNumber::with(['form', 'user', 'tasks', 'atualAprouver', 'atualTask', 'group'])->orderBy('id', 'desc');
        } else {
            $allForms = FormNumber::with(['form', 'user', 'tasks', 'atualAprouver', 'atualTask', 'group'])->where('user_id', auth()->user()->id)
                ->whereHas('tasks', function ($query) {
                    return $query->Where('create_user_id', auth()->user()->id)
                        ->orWhere('user_id', '=', auth()->user()->id)
                        ->orWhere('group', '=', auth()->user()->role_id);
                })->orderBy('id', 'desc');
        }

        if ($request->buscar != '' && $busca->numero != '') {
            //dd($busca->numero);
            $allForms->where('id', 'LIKE', '%' . $busca->numero . '%');
        }

        if ($request->buscar != '' && $busca->dataDe != '') {
            $allForms->where('created_at', '>', $busca->dataDe . ' 00:00:00.000');
        }

        if ($request->buscar != '' && $busca->dataAte != '') {
            $allForms->where('created_at', '<', $busca->dataAte . ' 23:59:59.000');
        }

        /*/
        Status do form:
            1 - Cancelado
            2 - Salvo esperando envio para aprovação
            3 - Enviado para aprovação, aguardando fluxo
            4 - Finalizado
        /*/

        if ($request->buscar != '' && $busca->aberto) {
            $allForms->whereIn('status_form', ['2', '3']);
        }

        if ($request->buscar != '' && $busca->finalizado) {
            $allForms->where('status_form', '4');
        }

        if ($request->buscar != '' && $busca->cancelado) {
            $allForms->where('status_form', '1');
        }

        return response()->json([
            'allForms' => $allForms->paginate(10),
        ]);
    }

    public function destroy($id, Request $request)
    {
        if (session('formsDelet')) {
            FormNumber::destroy($id);
            return back()->with([
                'title' => 'Deletado',
                'message' => 'Foi deletado.',
                'type' => 'alert-success'
            ]);
        }
    }
}
