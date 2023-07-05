<?php

namespace App\Http\Middleware;

use App\Models\Filial;
use App\Models\PushNotification;
use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Force\RolePermission;
use App\Models\UsuarioVendedor;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'flash' => [
                'title' => session('title'),
                'message' => session('message'),
                'type' => session('type'),
                'form' => session('form'),
                'form_number' => session('form_number')
            ],
            'controllersAccess' => ($request->user() != null) ? RolePermission::permissions($request->user()->role_id) : null,
            'notifications' => fn () => $request->user()
                ? PushNotification::where('user_id', $request->user()->id)->where('status', 1)->get()
                : null,
        ]);
    }
}
