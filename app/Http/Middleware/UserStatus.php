<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Http\Controllers\AllUsersController;
use Carbon\Carbon;

class UserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->status == '1') {
                $expireTime = Carbon::now()->addMinute(1); // keep online for 1 min
                Cache::put('is_online' . Auth::user()->id, true, $expireTime);

                //Last Seen
                User::where('id', Auth::user()->id)->update(['last_acess' => Carbon::now()]);
            } else {
                auth()->guard('web')->logout();
            }
        }
        return $next($request);
    }
}
