<?php

namespace App\Http\Middleware;

use Closure;

class UserAuthMiddleware
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
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        // $sessions = session()->all();
        // $sessions = array(session()->all());
        // $json = $request->input('json', null);
        // $params = json_decode($json);
        // $params_array = json_decode($json, true);
        // $passwordHash = hash('sha256', $params_array['password']);
        // $sessionExist = false;
        
        // foreach($sessions as $session){
        //     if($session['email'] == $params_array['email'] && $session['password'] == $passwordHash ){
        //         $sessionExist = true;
        //     }
        // }

        // if($checkToken && $sessionExist == true){
        if($checkToken){
            return $next($request);
        }else{
            $data = array(
                'status'=>'error',
                'code'=> 400,
                'message'=> 'User not correctly identified',                
            );
            return response()->json($data, $data['code']);
        }
        
    }
}
