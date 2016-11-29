<?php

namespace Skygdi\DeviceToken;

use Closure;
use Request;
use Validator;

class ApiTokenCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $validator = Validator::make(app('request')->header(), [
            'cookies' => 'required|max:65',
        ]);

        if( $validator->fails() ){
            return $this->un_authentication('token validation error');
        }

        if (!app('request')->header('cookies')) {
            return $this->un_authentication('missing token');
        }
        if( !DeviceTokenController::tokenCheck(app('request')->header('cookies')) ){
            return $this->un_authentication('unknow token');
        }

        return $next($request);
    }

    public function un_authentication($reason)
    {
        return response()->json(['state'=>$reason]);
    }


}
