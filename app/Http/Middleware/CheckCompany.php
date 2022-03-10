<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\Company_user;
use Closure;

class CheckCompany
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
//        $company=Company::where('slug',$request->route('company'))->first();
        $company=Company::where('slug',session('company'))->first();


        if(!$company){
            abort(403, 'La página no existe o no tiene permisos!');
        }
        $user_company=Company_user::where('company_id', $company->id)
            ->where('user_id', $request->user()->id)->first();
        if ($user_company===null) {
            abort(403, 'La página no existe o no tiene permisos!');
        }  else{
            return $next($request);
        }
        return $next($request);
    }
}