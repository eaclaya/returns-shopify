<?php

namespace App\Http\Middleware;

use App\Models\AccountSetting;
use App\Models\Currency;
use Closure;
use Illuminate\Http\Request;

class AccountCode
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
        $code = $request->route('code');
        if(!$code){
            abort(403);
        }
        $accountSettings = cache('accountSettings'); 
        if(!$accountSettings){
            $accountSettings = AccountSetting::with('account')->get()->keyBy('code')->toArray();
            cache(['accountSettings' => $accountSettings]);
        }
        $currencies = cache('currencies'); 
        if(!$currencies){
            $currencies = Currency::select('code', 'symbol', 'name', 'rate')->get()->keyBy('code')->toArray();
            cache(['currencies' => $currencies]);
        }
        if(isset($accountSettings[$code]) == false){
            $accountSettings = AccountSetting::with('account')->get()->keyBy('code')->toArray();
            if(isset($accountSettings[$code]) == false){
                abort(403);
            }
            cache(['accountSettings' => $accountSettings]);
        }
        $accountSetting = $accountSettings[$code];
        session()->put('ACCOUNT_ID', $accountSetting['account_id']);
        session()->put('ACCOUNT_CODE', $accountSetting['code']);
        
        return $next($request);
    }
}
