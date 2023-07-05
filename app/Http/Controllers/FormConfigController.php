<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Form;
use App\Models\TaskForm;
use Illuminate\Support\Facades\Validator;

//tipos de status das tarefas
//1 - inicio
//2 - atividade
//3 - se
//4 - encerrado

class FormConfigController extends Controller
{
    public function index()
    {
        if (auth()->user()->role_id == 1) {
            $forms = Form::get();

            return Inertia::render(
                'FormFluig/Config/Index',
                [
                    'forms' => $forms
                ]
            );
        }

        return redirect('/');
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            "name" => 'required',
            'slung' => 'required'
        ])->validate();

        Form::create([
            "name" => $request->name,
            'slung' => $request->slung,
            'status' => 1
        ]);

        return back()->with([
            'title' => 'Formulário criado',
            'message' => 'Criado com sucesso.',
            'type' => 'alert-success'
        ]);
    }

    public function configView($id)
    {
        if (auth()->user()->role_id == 1) {
            $form = Form::where('id', $id)->with('Fluxo')->first();

            return Inertia::render(
                'FormFluig/Config/Config',
                [
                    'formInfo' => $form
                ]
            );
        }
    }

    public function storeTask(Request $request)
    {
        $verificaInicioFim = TaskForm::where('form_id', $request->form_id)->get();

        if (count($verificaInicioFim) == 0) {
            //cria inicio e fim
            $this->criaInicioFim($request->form_id);
        }

        $tarefa = TaskForm::create([
            'name' => $request->name,
            'ordem' => $request->ordem,
            'type' => $request->type,
            'user_id' => $request->user_id,
            'group' => $request->group,
            'form_id' => $request->form_id,
            'status' => 1
        ]);

        $this->ordenarTudo($request->form_id, $request->ordem, $tarefa->id);

        return back(303);
    }

    public function delet($id)
    {
        $formulario = TaskForm::where('id', $id)->first()->form_id;
        $deleta = TaskForm::destroy($id);
        $this->reordena($formulario);

        return back(303);
    }

    public function edit(Request $request)
    {
        $edit = TaskForm::find($request->id);

        $edit->name = $request->name;
        $edit->ordem = $request->ordem;
        $edit->type = $request->type;

        if ($request->user_id != null)
            $edit->user_id = $request->user_id;

        $edit->save();

        $this->ordenarTudo($edit->form_id, $request->ordem, $edit->id);

        return back(303);
    }

    static function criaInicioFim($form_id)
    {
        TaskForm::create([
            'name' => 'Inicio',
            'ordem' => '1',
            'type' => '1',
            'user_id' => '1',
            'form_id' => $form_id,
            'status' => 1
        ]);

        TaskForm::create([
            'name' => 'Finalizar',
            'ordem' => '2',
            'type' => '4',
            'user_id' => '1',
            'form_id' => $form_id,
            'status' => 1
        ]);
    }

    static function reordena($form)
    {
        $ordenar = TaskForm::where('form_id', $form)->get();

        for ($i = 0; count($ordenar) > $i; $i++) {
            $nudaOrderm = TaskForm::find($ordenar[$i]->id);
            $nudaOrderm->ordem = $i + 1;
            $nudaOrderm->save();
        }

        $ordenar = TaskForm::where('form_id', $form)->get();

        // $ordenarFim = TaskForm::where('form_id', $form_id)->where('type', '4')->first();
        // $ordemFim = TaskForm::find($ordenarFim->id);
        // $ordemFim->ordem = count($ordenar);
        // $ordemFim->save();
    }

    static function ordenarTudo($form_id, $ordem, $tarefa_id)
    {
        $ordenar = TaskForm::where('form_id', $form_id)->get();

        for ($i = 0; count($ordenar) > $i; $i++) {
            if ($tarefa_id != $ordenar[$i]->id && $ordenar[$i]->type != '4') {
                if ($ordenar[$i]->ordem == $ordem) {
                    $nudaOrderm = TaskForm::find($ordenar[$i]->id);
                    $nudaOrderm->ordem = $ordem + 1;
                    $nudaOrderm->save();
                } else {
                    $nudaOrderm = TaskForm::find($ordenar[$i]->id);
                    $nudaOrderm->ordem = $i + 1;
                    $nudaOrderm->save();
                }
            }
        }

        $ordenarFim = TaskForm::where('form_id', $form_id)->where('type', '4')->first();
        $ordemFim = TaskForm::find($ordenarFim->id);
        $ordemFim->ordem = count($ordenar);
        $ordemFim->save();
    }
}
