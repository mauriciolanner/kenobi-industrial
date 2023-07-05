<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormNumber;
use App\Models\PushNotification;
use App\Models\User;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailUser;

class PushNotificationController extends Controller
{
    public function index()
    {
        $notifications  = PushNotification::where('user_id', auth()->user()->id)->get();
        return response()->json($notifications);
    }

    //notificações de aprovação
    public function opemTask()
    {
        $notifications = FormNumber::where('atual_user', auth()->user()->id)->with('form')
            ->whereIn('status_form', ['2', '3'])
            ->orWhere('atual_group', auth()->user()->role_id)->orderBy('id', 'desc')->get();
        return response()->json($notifications);
    }

    public function delete($id)
    {
        $notification = PushNotification::where('user_id', auth()->user()->id)->where('id', $id)->first();

        if ($notification != null) {
            PushNotification::destroy($id);
            return response()->json(['response' => true]);
        } else {
            return response()->json(['response' => false]);
        }
    }

    public function deleteAll()
    {
        $notification = PushNotification::where('user_id', auth()->user()->id)->get();

        foreach ($notification as $notificate) {
            PushNotification::destroy($notificate->id);
        }

        return response()->json(['response' => true]);
    }
}
