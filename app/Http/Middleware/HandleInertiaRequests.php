<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        $settings = is_array(cache('accountSettings'))  ? cache('accountSettings') : [];
        $settings = isset($settings[$request->route('code')]) ? $settings[$request->route('code')] : null;
        if($settings){
            $settings['account']['domain'] = "https://".$settings['account']['domain'];
        }
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'settings' => $settings,
            'prefix' => $request->route('code')
        ]);
    }
}
