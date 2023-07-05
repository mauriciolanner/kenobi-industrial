<?php

namespace App\Forms;

use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use App\Models\LogForm;
use App\Models\Approver;
use App\Models\FormNumber;
use App\Force\PushNotificationEmail;
use App\Models\Role;

/*/
    Status do form:
        1 - Cancelado
        2 - Salvo esperando envio para aprovação
        3 - Enviado para aprovação, aguardando fluxo
        4 - Finalizado
    /*/

/*/
    tipos das tarefas
        1 - Atividade de inicio
        2 - atividade a usuário unico
        3 - atividade atribuido a Grupo
        4 - atividade fim
        5 - Atividade em Looping

        status da tarefa
        1 - aberta
        2 - finalizada
        0 - cancelada
    /*/

class Functions
{
    public $model;
    public $formInfo;
    public $childs = null;
    public $attachments = null;
    public $aproverInput = null;
    public $taskAproverInput = null;
    public $childChilds = null;

    //
    public function store($request)
    {
        $textoStatus = '';

        if ($request->status == 3)
            $textoStatus = 'Enviado para aprovação, aguardando fluxo';
        if ($request->status == 2)
            $textoStatus = 'Salvo esperando envio para aprovação';

        $createFormNumber = FormNumber::create(
            [
                'form_id' => $this->formInfo->id,
                'form_opem_id' => 0,
                'user_id' => auth()->user()->id,
                'status_form' => $request->status,
                'task' =>  $textoStatus,
                'status' => 0
            ]
        );

        $objeto = (object)$request->all();
        $objeto->form_number = $createFormNumber->id;
        $objeto->create_user_id = auth()->user()->id;
        $objeto->form_id = $this->formInfo->id;

        $formPrincipal = ucfirst(strtolower('App\Models\FormModels\\' . $this->model))::create(collect($objeto)->toArray());

        $createFormNumber->form_opem_id = $formPrincipal->id;
        $createFormNumber->status = 1;
        $createFormNumber->save();

        if ($this->childs != null) {
            foreach ($request->childs as $child) {
                $objetoChild = (object)$child;
                $objetoChild->form_number = $createFormNumber->id;
                $objetoChild->create_user_id = auth()->user()->id;
                $objetoChild->status = '1';

                $Newchild = ucfirst(strtolower('App\Models\FormModels\\' . $this->childs))::create(collect($objetoChild)->toArray());

                if ($this->childChilds != null) {
                    foreach ($child['childs'] as $childChild) {
                        $objetoChildChild = (object)$childChild;
                        $objetoChildChild->form_number = $createFormNumber->id;
                        $objetoChildChild->filho_id = $Newchild->id;
                        $objetoChildChild->create_user_id = auth()->user()->id;
                        $objetoChildChild->status = '1';
                        ucfirst(strtolower('App\Models\FormModels\\' . $this->childChilds))::create(collect($objetoChildChild)->toArray());
                    }
                }
            }
        }

        if ($request->status == 2) {
            LogForm::create([
                'form_number' => $createFormNumber->id,
                'description' => 'Salvo pelo usuário ' . auth()->user()->name,
                'occurrence' => $request->taskOccurrence
            ]);

            return $createFormNumber->id;
        } else if ($request->status == 3) {
            if ($this->taskAproverInput != null)
                $statusFinal = $this->createApproval($this->formInfo->slung, $createFormNumber->id, $this->taskAproverInput, $request[$this->aproverInput]);
            else
                $statusFinal = $this->createApproval($this->formInfo->slung, $createFormNumber->id);

            $atualTask = Approver::where('form_number', $createFormNumber->id)->where('status', '1')->first();

            $formOpem = ucfirst(strtolower('App\Models\FormModels\\' . $this->model))::where('form_number', $createFormNumber->id)
                ->with('formulario', 'aprovacao', 'formularioNumero', 'childs', 'aprovacao.userApprover', 'aprovacao.groupDetail', 'historico')->first();

            if ($atualTask->user_id != null) {
                $createFormNumber->atual_user = $atualTask->user_id;

                $objetoPush = (object)[
                    'user_id' => $atualTask->user_id,
                    'title' => "Formulário para aprovar",
                    'message' => "Um formulário de " . $formOpem->formulario->name . " esta para sua aprovação. Fomulário número " . $createFormNumber->id,
                    'link' => env('APP_URL') . "/form//" . $formOpem->formulario->slung . "/detalhes//" . $createFormNumber->id,
                    'form_number' => $createFormNumber->id,
                    'status' => '1'
                ];
                PushNotificationEmail::push($objetoPush);
            } else if ($atualTask->group != null) {
                $createFormNumber->group;

                $usersGrup = Role::where('id', $atualTask->group)->with('users')->first()->users;
                foreach ($usersGrup as $user) {
                    $objetoPush = (object)[
                        'user_id' => $user->id,
                        'title' => "Formulário para aprovar",
                        'message' => "Um formulário de " . $formOpem->formulario->name . " esta para sua aprovação. Fomulário número " . $createFormNumber->id,
                        'link' => env('APP_URL') . "/form//" . $formOpem->formulario->slung . "/detalhes//" . $createFormNumber->id,
                        'form_number' => $createFormNumber->id,
                        'status' => '1'
                    ];
                    PushNotificationEmail::push($objetoPush);
                }
            }

            $createFormNumber->atual_task = $atualTask->id;
            //$createFormNumber->atual_group = $request->carteira;
            $createFormNumber->task = $atualTask->name;
            $createFormNumber->save();

            LogForm::create([
                'form_number' => $createFormNumber->id,
                'description' => 'Enviado para aprovação pelo usuário ' . auth()->user()->name,
                'occurrence' => $request->taskOccurrence
            ]);

            return $createFormNumber->id;
        }
    }

    public function update($request)
    {
        $objeto = (object)$request->all();

        unset($objeto->taskOccurrence);
        unset($objeto->form_number);
        unset($objeto->create_user_id);
        unset($objeto->form_id);
        unset($objeto->id);
        unset($objeto->childs);

        $editaForm = ucfirst(strtolower('App\Models\FormModels\\' . $this->model))::where('id', $request->id)
            ->update(collect($objeto)->toArray());

        $editaForm = ucfirst(strtolower('App\Models\FormModels\\' . $this->model))::find($request->id);

        if ($this->childs != null) {
            if ($this->childChilds != null)
                ucfirst(strtolower('App\Models\FormModels\\' . $this->childChilds))::where('form_number', $request->form_number)->delete();

            ucfirst(strtolower('App\Models\FormModels\\' . $this->childs))::where('form_number', $request->form_number)->delete();

            foreach ($request->childs as $child) {
                $objetoChild = (object)$child;
                $objetoChild->form_number = $request->form_number;
                $objetoChild->create_user_id = auth()->user()->id;
                $objetoChild->status = '1';

                $Newchild = ucfirst(strtolower('App\Models\FormModels\\' . $this->childs))::create(collect($objetoChild)->toArray());

                if ($this->childChilds != null) {
                    foreach ($child['childs'] as $childChild) {
                        $objetoChildChild = (object)$childChild;
                        $objetoChildChild->form_number = $request->form_number;
                        $objetoChildChild->filho_id = $Newchild->id;
                        $objetoChildChild->create_user_id = auth()->user()->id;
                        $objetoChildChild->status = '1';
                        ucfirst(strtolower('App\Models\FormModels\\' . $this->childChilds))::create(collect($objetoChildChild)->toArray());
                    }
                }
            }
        }

        if ($request->status == 2) {
            $atualiza = FormNumber::find($editaForm->form_number);
            $atualiza->task = 'Salvo esperando envio para aprovação';
            $atualiza->save();

            LogForm::create([
                'form_number' => $editaForm->form_number,
                'description' => 'Salvo pelo usuário ' . auth()->user()->name,
                'occurrence' => $request->taskOccurrence
            ]);

            return $editaForm->form_number;
        } else if ($request->status == 4) {
            $atualiza = FormNumber::find($editaForm->form_number);
            $atualiza->task = 'Salvo pelo usuário ' . auth()->user()->name;
            $atualiza->save();

            LogForm::create([
                'form_number' => $editaForm->form_number,
                'description' => 'Salvo pelo usuário ' . auth()->user()->name,
                'occurrence' => $request->taskOccurrence
            ]);

            return $editaForm->form_number;
        } else if ($request->status == 3) {

            if ($this->taskAproverInput != null)
                $statusFinal = $this->createApproval($this->formInfo->slung, $editaForm->form_number, $request->aprovador, $this->taskAproverInput, $request[$this->aproverInput]);
            else
                $statusFinal = $this->createApproval($this->formInfo->slung, $editaForm->form_number, $request->aprovador);

            $atualTask = Approver::where('form_number', $editaForm->form_number)->where('status', '1')->first();

            $atualiza = FormNumber::find($editaForm->form_number);
            $atualiza->task = $atualTask->name;
            $atualiza->atual_task = $atualTask->id;
            $atualiza->atual_group = $request->carteira;
            $atualiza->status_form = $request->status;
            $atualiza->save();

            LogForm::create([
                'form_number' => $editaForm->form_number,
                'description' => 'Enviado para aprovação pelo usuário ' . auth()->user()->name,
                'occurrence' => $request->taskOccurrence
            ]);

            return $editaForm->form_number;
        }
        return false;
    }

    public function toAprove($request)
    {
        $atualTask = Approver::where('form_number', $request->form_number)->where('status', '1')->first();

        if ($atualTask != null) {
            $approver = $this->verificaApprover($atualTask);

            if ($approver) {
                if ($atualTask->type == 5) {
                    //em looping

                    $loop = false;

                    if ($request[$this->aproverInput] != null) {
                        $aproverUser = $request[$this->aproverInput];

                        $reordem = Approver::where('form_number', $request->form_number)->where('ordem', '>', $atualTask->ordem)->get();

                        foreach ($reordem as $ordem) {
                            Approver::where('id', $ordem->id)->update([
                                'ordem' => intval($ordem->ordem) + 1,
                            ]);
                        }

                        $newAproval = Approver::create([
                            'name' => $atualTask->name,
                            'ordem' => intval($atualTask->ordem) + 1,
                            'type' => $atualTask->type,
                            'user_id' => $aproverUser,
                            'form_id' => $atualTask->form_id,
                            'status' => 1,
                            'task_forms_id' => $atualTask->task_forms_id,
                            'form_number' => $request->form_number,
                            'create_user_id' => $atualTask->create_user_id
                        ]);

                        $loop = true;
                    }

                    $aprovaUpdate = Approver::find($atualTask->id);
                    $aprovaUpdate->status = 2;
                    $aprovaUpdate->save();

                    $proximaTarefa = Approver::where('form_number', $request->form_number)->where('status', '1')->first();

                    $formOpem = ucfirst(strtolower('App\Models\FormModels\\' . $this->model))::where('form_number', $request->form_number)
                        ->with('formulario', 'aprovacao', 'formularioNumero', 'childs', 'aprovacao.userApprover', 'aprovacao.groupDetail', 'historico')->first();

                    LogForm::create([
                        'form_number' => $atualTask->form_number,
                        'description' => 'Aprovado pelo usuário ' . auth()->user()->name,
                        'occurrence' => $request->taskOccurrence
                    ]);

                    $objetoPush = (object)[
                        'user_id' => $request->create_user_id,
                        'title' => "Formulário aprovado",
                        'message' => "Um formulário de " . $formOpem->formulario->name . " seu foi aprovado. Fomulário número " . $formOpem->form_number,
                        'link' => env('APP_URL') . "/form//" . $formOpem->formulario->slung . "/detalhes//" . $formOpem->form_number,
                        'form_number' => $request->form_number,
                        'status' => '1'
                    ];
                    PushNotificationEmail::push($objetoPush);

                    if ($loop) {
                        LogForm::create([
                            'form_number' => $atualTask->form_number,
                            'description' => 'Nova aprovação para ' . User::where('id', $aproverUser)->first()->name,
                            'occurrence' => $request->taskOccurrence
                        ]);

                        $objetoPush = (object)[
                            'user_id' => $proximaTarefa->user_id,
                            'title' => "Formulário para aprovar",
                            'message' => "Um formulário de " . $formOpem->formulario->name . " esta para sua aprovação. Fomulário número " . $request->form_number,
                            'link' => env('APP_URL') . "/form//" . $formOpem->formulario->slung . "/detalhes//" . $request->form_number,
                            'form_number' => $request->form_number,
                            'status' => '1'
                        ];
                        PushNotificationEmail::push($objetoPush);

                        return $atualTask->form_number;
                    }
                } else {
                    $aprovaUpdate = Approver::find($atualTask->id);
                    $aprovaUpdate->status = 2;
                    $aprovaUpdate->save();

                    $proximaTarefa = Approver::where('form_number', $request->form_number)->where('status', '1')->first();
                }


                if ($proximaTarefa->type == 4) {
                    //finalizando o processo por fluxo de aprovação
                    $formUpdate = ucfirst(strtolower('App\Models\FormModels\\' . $this->model))::where('form_number', $request->form_number);
                    $formUpdate->update(['status' => 4]);

                    $atualiza = FormNumber::find($request->form_number);
                    $atualiza->status_form = 4;
                    $atualiza->atual_task = null;
                    $atualiza->atual_user = null;
                    $atualiza->atual_group = null;
                    $atualiza->task = 'Finalizado';
                    $atualiza->save();

                    $finalizaUpdate = Approver::find($proximaTarefa->id);
                    $finalizaUpdate->status = 2;
                    $finalizaUpdate->save();

                    LogForm::create([
                        'form_number' => $atualTask->form_number,
                        'description' => 'Aprovado pelo usuário ' . auth()->user()->name,
                        'occurrence' => $request->taskOccurrence
                    ]);

                    LogForm::create([
                        'form_number' => $atualTask->form_number,
                        'description' => 'Finalizado por fluxo',
                        'occurrence' => $request->taskOccurrence
                    ]);

                    $formOpem = ucfirst(strtolower('App\Models\FormModels\\' . $this->model))::where('form_number', $request->form_number)
                        ->with('formulario', 'aprovacao', 'formularioNumero', 'childs', 'aprovacao.userApprover', 'aprovacao.groupDetail', 'historico')->first();

                    $objetoPush = (object)[
                        'user_id' => $formOpem->create_user_id,
                        'title' => "Formulário aprovado e finalizado",
                        'message' => "Um formulário de " . $formOpem->formulario->name . " seu foi aprovado e finalizado. Fomulário número " . $request->form_number,
                        'link' => env('APP_URL') . "/form//" . $formOpem->formulario->slung . "/detalhes//" . $request->form_number,
                        'form_number' => $request->form_number,
                        'status' => '1'
                    ];
                    PushNotificationEmail::push($objetoPush);

                    return $atualTask->form_number;
                } else {
                    //Segue o fluxo
                    LogForm::create([
                        'form_number' => $atualTask->form_number,
                        'description' => 'Aprovado pelo usuário ' . auth()->user()->name,
                        'occurrence' => $request->taskOccurrence
                    ]);

                    //notifica o usuário que abriu o form

                    $formOpem = ucfirst(strtolower('App\Models\FormModels\\' . $this->model))::where('form_number', $request->form_number)
                        ->with('formulario', 'aprovacao', 'formularioNumero', 'childs', 'aprovacao.userApprover', 'aprovacao.groupDetail', 'historico')->first();

                    $objetoPush = (object)[
                        'user_id' => $formOpem->create_user_id,
                        'title' => "Formulário aprovado",
                        'message' => "Um formulário de " . $formOpem->formulario->name . " seu foi aprovado. Fomulário número " . $request->form_number,
                        'link' => env('APP_URL') . "/form//" . $formOpem->formulario->slung . "/detalhes//" . $request->form_number,
                        'form_number' => $request->form_number,
                        'status' => '1'
                    ];
                    PushNotificationEmail::push($objetoPush);

                    //notifica o proximo aprovador ou grupo
                    if ($proximaTarefa->user_id != null) {
                        $objetoPush = (object)[
                            'user_id' => $proximaTarefa->user_id,
                            'title' => "Formulário para aprovar",
                            'message' => "Um formulário de " . $formOpem->formulario->name . " esta para sua aprovação. Fomulário número " . $request->form_number,
                            'link' => env('APP_URL') . "/form//" . $formOpem->formulario->slung . "/detalhes//" . $request->form_number,
                            'form_number' => $request->form_number,
                            'status' => '1'
                        ];
                        PushNotificationEmail::push($objetoPush);
                    } else if ($proximaTarefa->group != null) {
                        $usersGrup = Role::where('id', $proximaTarefa->group)->with('users')->first()->users;
                        foreach ($usersGrup as $user) {
                            $objetoPush = (object)[
                                'user_id' => $user->id,
                                'title' => "Formulário para aprovar",
                                'message' => "Um formulário de " . $formOpem->formulario->name . " esta para sua aprovação. Fomulário número " . $request->form_number,
                                'link' => env('APP_URL') . "/form//" . $formOpem->formulario->slung . "/detalhes//" . $request->form_number,
                                'form_number' => $request->form_number,
                                'status' => '1'
                            ];
                            PushNotificationEmail::push($objetoPush);
                        }
                    }
                    $nextTask = Approver::where('form_number', $request->form_number)->where('status', '1')->first();
                    $atualiza = FormNumber::find($request->form_number);
                    $atualiza->atual_task = $nextTask->id;
                    $atualiza->task = $nextTask->name;
                    $atualiza->atual_group = $nextTask->group;
                    $atualiza->atual_user = $nextTask->usr_id;
                    $atualiza->save();

                    return $atualTask->form_number;
                }
            }

            return false;
        }

        return false;
    }

    public function toCancel($request)
    {
        $atualTask = Approver::where('form_number', $request->form_number)->where('status', '1')->first();
        $approver = $this->verificaApprover($atualTask);

        if ($approver) {
            $cancelar = Approver::where('form_number', $request->form_number)->where('status', '1')->update([
                'status' => 0
            ]);

            $formUpdate = ucfirst(strtolower('App\Models\FormModels\\' . $this->model))::where('form_number', $request->form_number);
            $formUpdate->update(['status' => 1]);

            $atualiza = FormNumber::find($request->form_number);
            $atualiza->task = 'Cancelado';
            $atualiza->status_form = 1;
            $atualiza->atual_task = null;
            $atualiza->atual_user = null;
            $atualiza->atual_group = null;
            $atualiza->save();

            LogForm::create([
                'form_number' => $request->form_number,
                'description' => 'Cancelado por ' . auth()->user()->name,
                'occurrence' => $request->taskOccurrence
            ]);

            $formOpem = ucfirst(strtolower('App\Models\FormModels\\' . $this->model))::where('form_number', $request->form_number)
                ->with('formulario', 'aprovacao', 'formularioNumero', 'childs', 'aprovacao.userApprover', 'aprovacao.groupDetail', 'historico')->first();
            $notificationUsers = $this->allInvolved($formOpem);

            foreach ($notificationUsers as $user) {
                $objetoPush = (object)[
                    'user_id' => $user,
                    'title' => "Formulário reprovado",
                    'message' => "Um formulário de " . $formOpem->formulario->name . " seu foi reprovado. Fomulário número " . $request->form_number,
                    'link' => env('APP_URL') . "/form//" . $formOpem->formulario->slung . "/detalhes//" . $request->form_number,
                    'form_number' => $request->form_number,
                    'status' => '1'
                ];
                PushNotificationEmail::push($objetoPush);
            }

            return $request->form_number;
        }

        return false;
    }

    public function toRevise($request)
    {
        $revisar = Approver::where('form_number', $request->form_number)->delete();
        $this->formInfo = ucfirst(strtolower('App\Models\FormModels\\' . $this->model))::where('form_number', $request->form_number);

        $atualiza = FormNumber::find($request->form_number);
        $atualiza->task = 'Revisão';
        $atualiza->status_form = 2;
        $atualiza->save();

        $this->formInfo->update(['status' => 2]);

        LogForm::create([
            'form_number' => $request->form_number,
            'description' => 'Enviado a revisão por ' . auth()->user()->name,
            'occurrence' => $request->taskOccurrence
        ]);

        return  $request->form_number;
    }

    public function sendToTask($request, $task_order)
    {
        $atualTask = Approver::where('form_number', $request->form_number)->where('status', '1')->first();
        $approver = $this->verificaApprover($atualTask);

        if ($approver) {
            $tasks = Approver::where('form_number', $request->form_number)->where('ordem', $task_order)->update(['status' => 1]);

            LogForm::create([
                'form_number' => $request->form_number,
                'description' => 'Retornado a tarefa ' . Approver::where('form_number', $request->form_number)->where('ordem', $task_order)->first()->name . ' por ' . auth()->user()->name,
                'occurrence' => $request->taskOccurrence
            ]);

            return $request->form_number;
        }

        return false;
    }

    static function createApproval(
        $id,
        $formNumber,
        $taskAproverInput = null,
        $aproverInput = null
    ) {
        $formInfo = Form::where('slung', $id)->with('fluxo')->first();

        $verificaAprovacao = Approver::where('form_number', $formNumber)->get();
        $retorno = '';

        if (count($verificaAprovacao) == 0) {
            $status = 1;
            foreach ($formInfo->fluxo as $index => $fluxo) {
                $user = $fluxo->user_id;
                ($index == 0) ? $status = 2 : $status = 1;
                ($index == 0) ? $user = auth()->user()->id : $user = $fluxo->user_id;
                ($index == 1) ? $retorno = $fluxo->name : $retorno = $fluxo->name;

                if ($fluxo->type == 2 || $fluxo->type == 5) {
                    $aproverUser = $fluxo->user_id;

                    if ($taskAproverInput != null) {
                        $aproverUser = $aproverInput;
                    }

                    Approver::create([
                        'name' => $fluxo->name,
                        'ordem' => $fluxo->ordem,
                        'type' => $fluxo->type,
                        'user_id' => $aproverUser,
                        'form_id' => $fluxo->form_id,
                        'status' => $status,
                        'task_forms_id' => $fluxo->id,
                        'form_number' => $formNumber,
                        'create_user_id' => auth()->user()->id
                    ]);
                } else  if ($fluxo->type == 3) {
                    Approver::create([
                        'name' => $fluxo->name,
                        'ordem' => $fluxo->ordem,
                        'type' => $fluxo->type,
                        'form_id' => $fluxo->form_id,
                        'status' => $status,
                        'task_forms_id' => $fluxo->id,
                        'form_number' => $formNumber,
                        'group' => $fluxo->group,
                        'create_user_id' => auth()->user()->id
                    ]);
                } else {
                    Approver::create([
                        'name' => $fluxo->name,
                        'ordem' => $fluxo->ordem,
                        'type' => $fluxo->type,
                        'form_id' => $fluxo->form_id,
                        'status' => $status,
                        'task_forms_id' => $fluxo->id,
                        'form_number' => $formNumber,
                        'create_user_id' => auth()->user()->id
                    ]);
                }
            }
        }

        return $retorno;
    }

    public function toFinish($request, $justificativa)
    {
        $formUpdate = ucfirst(strtolower('App\Models\FormModels\\' . $this->model))::where('form_number', $request->form_number);
        $formUpdate->update(['status' => 4]);

        $atualiza = FormNumber::find($request->form_number);
        $atualiza->status_form = 4;
        $atualiza->atual_task = null;
        $atualiza->atual_user = null;
        $atualiza->atual_group = null;
        $atualiza->task = 'Finalizado';
        $atualiza->save();

        LogForm::create([
            'form_number' => $request->form_number,
            'description' => 'Finalizado por processo',
            'occurrence' => $justificativa
        ]);
    }

    static function verificaApprover($atualTask)
    {
        if ($atualTask == null)
            return false;

        if (Auth()->user()->role_id == 1)
            return true;

        if ($atualTask->user_id == Auth()->user()->id)
            return true;

        if ($atualTask->group == Auth()->user()->role_id)
            return true;

        return false;
    }

    static function verificaView($formOpem)
    {
        if (Auth()->user()->role_id == 1)
            return true;

        if ($formOpem->create_user_id == Auth()->user()->id)
            return true;

        if (session('borderoEdit')) {
            return true;
        }

        foreach ($formOpem->aprovacao as $aprovacao) {
            if ($aprovacao->user_id == Auth()->user()->id)
                return true;
        }

        return false;
    }

    static function allInvolved($formOpem)
    {
        $users = [];
        array_push($users, $formOpem->create_user_id);
        foreach ($formOpem->aprovacao as $aprovador) {
            if ($aprovador->user_id != null)
                array_push($users, $aprovador->user_id);
            else if ($aprovador->group != null) {
                $usersGrup = Role::where('id', $aprovador->group)->with('users')->first()->users;
                foreach ($usersGrup as $user)
                    array_push($users, $user->id);
            }
        }

        return $users;
    }
}
