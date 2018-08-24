<?php

namespace App\Http\Middleware;

use Closure;

class Filter_UrlMiddleware
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

//        var_dump($request);




//        var_dump($request->input('_url'));
//        $request->flashExcept('_token._url');
//        $request = $request->except('_url');


//        $data = $request->except(['_token','_url']);
//        var_dump($data);

//        if(isset($data['_url'])){
//            unset($data['_url']);
//        }

        return $next($request);
    }
}
