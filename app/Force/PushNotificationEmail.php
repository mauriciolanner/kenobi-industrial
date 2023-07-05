<?php

namespace App\Force;

use App\Models\PushNotification;
use App\Models\User;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailUser;

class PushNotificationEmail
{
    //
    static function push($notificate)
    {
        //cria a push
        PushNotification::create([
            'user_id' => $notificate->user_id,
            'title' => $notificate->title,
            'message' => $notificate->message,
            'link' => $notificate->link,
            'type' => '1',
            'form_number' => $notificate->form_number,
            'status' => $notificate->status
        ]);

        //envia o email
        // $user = User::find($notificate->user_id);
        // $subject = "BomixForce - " . $notificate->title;
        // $text = $notificate->message;
        //Mail::to($user->email)->send(new SendMailUser($user, $text, $subject, $notificate->link));
    }
}
