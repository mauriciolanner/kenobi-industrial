<?php

namespace App\Force;

use App\Models\Role;

class RolePermission
{
    static function permissions($id)
    {
        $rolesPermission = Role::where('id', $id)
            ->with('controllers', 'controllers.controller')->first();

        //dd($rolesPermission->controllers);

        $arrayper = [];
        $arrayReturn = null;
        $id = $rolesPermission->id;

        foreach ($rolesPermission->controllers as $persmission) {
            $varview = $persmission->controller->slung . 'View';
            $varadd = $persmission->controller->slung . 'Add';
            $varedit = $persmission->controller->slung . 'Edit';
            $vardelet = $persmission->controller->slung . 'Delet';
            $arrayper = $arrayper + [
                'name' => $rolesPermission->name,
                'id' => $rolesPermission->id,
                $varview => $persmission->view,
                $varadd => $persmission->add,
                $varedit => $persmission->edit,
                $vardelet => $persmission->delet,
            ];
        }
        session($arrayper);
        return $arrayper;
    }
}
