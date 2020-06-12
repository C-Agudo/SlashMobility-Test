<?php

namespace App\Http\Controllers;

use App\Mail\confirmationEmail;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Str;
use Mail;
use Validator;

class UserController extends Controller
{
    public function register(Request $request){
        
        //Catch the request
        $json = $request->input('json');
        // $json = $request->input('json');
        // $json = str_replace('&quot;', '"', $json);
        $params = json_decode($json);
        $params_array = json_decode($json,true);


        //Validation
        $validate= Validator::make($params_array,[
            'user_name' =>'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validate->fails()){
            $responseData = array(
                'status'=>'error',
                'code'=> 404,
                'message'=> 'invalid data',
                'errors'=> $validate->errors()
            );
        }else{
            
            //Password encriptation
            $passwordHash = hash('sha256', $params->password);

            //Crate confirmation code
            $confirmationCode = Str::random(25);

            //Create and save user
            $user = new User();
            $user->user_name = $params_array['user_name'];
            $user->email = $params_array['email'];
            $user->password = $passwordHash;
            $user->confirmed = false;
            $user->confirmation_code = $confirmationCode;
            if(isset($params_array['name'])){
                $user->name = $params_array['name'];
            };
            if(isset($params_array['lastname'])){
                $user->name = $params_array['lastname'];
            };
            if(isset($params_array['phone'])){
                $user->name = $params_array['phone'];
            };
            if(isset($params_array['position'])){
                $user->name = $params_array['position'];
            };

            $user->save();

            //Send confirmation code
            Mail::to($user['email'])->send(new confirmationEmail($user));
            
            $responseData = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> 'User create correctly',
            );
        }
        return response()->json($responseData, $responseData['code']);
    }

    public function verify($code){

        $user = User::where('confirmation_code', $code)->first();

        if (! $user){
            $responseData = array(
                'status'=>'error',
                'code'=> 404,
                'message'=> 'invalid data',
            );
        }else{
            $user->confirmed = true;
            $user->confirmation_code = null;
            $user->save();

            $responseData = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> 'Email confirmed correctly',
            );
        }
            
        return response()->json($responseData, $responseData['code']);
    }

    public function login(Request $request) {

        $jwtAuth = new \JwtAuth();
        
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        //Validation
        $validate= Validator::make($params_array,[
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        //Match coincidences
        if($validate->fails()){
            $signup = array(
                'status'=>'error',
                'code'=> 404,
                'message'=> 'invalid login',
                'errors'=> $validate->errors()
            );
        }else{
            $passwordHash = hash('sha256', $params->password);
            $signupJwt = $jwtAuth->signup($params->email, $passwordHash);
            $request->session()->keep([
                'email'=> $params->email,
                'password'=> $passwordHash
            ]);
            $signup = array(
                'status'=>'error',
                'code'=> 200,
                'message'=> 'corrrect login',
                'signupJwtrs'=> $signupJwt
            );

            if(isset($params->gettoken)){
                $signupJwt = $jwtAuth->signup($params->email, $passwordHash,true);
                $request->session()->keep([
                    'email'=> $params->email,
                    'password'=> $passwordHash
                    
                ]);
                $signup = array(
                    'status'=>'error',
                    'code'=> 200,
                    'message'=> 'corrrect login',
                    'signupJwtrs'=> $signupJwt
                );
            }
        };
        return response()->json($signup);
    }

    public function logout(Request $request){
        $sessions = array(session()->all());
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        $passwordHash = hash('sha256', $params->password);

        foreach($sessions as $session){
            if($session['email'] == $params_array['email'] && $session['password'] == $passwordHash ){
                session()->forget([
                    'email',
                    'password'
                ]);
                $logout = true;
            }
        }
        if (isset($logout)){
            $logout = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> 'correct logout',
            );
        }else{
            $logout = array(
                'status'=>'error',
                'code'=> 404,
                'message'=> 'incorrect logout',
            );
        }
        return response()->json($logout);
    }

    public function updatePassword(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        if($checkToken){

            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);
            $user = $jwtAuth->checkToken($token, true);
            $passwordHash = hash('sha256', $params->password);

            $validate= Validator::make($params_array,[
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user_update = User::where('email', $user->email)->update(array('password'=>$passwordHash));
            
            $update = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> $user_update,                
            );

        }else{
            $update = array(
                'status'=>'error',
                'code'=> 400,
                'message'=> 'User not correctly identified',                
            );
        }
        return response()->json($update, $update['code']);
    }

    public function list(){
        $userList = User::all();
        if(isset($userList)){
            $list = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> $userList,                
            );
        }else{
            $list = array(
                'status'=>'error',
                'code'=> 400,
                'message'=> 'Field not found',                
            );
        }
        return response()->json($list, $list['code']);
    }

    public function detail($userName, Request $request ){

            $user = User::all()->whereIn('user_name', [$userName]);

            $detail = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> $user,
            );
        
        return response()->json($detail, $detail['code']);
    }

    public function update(Request $request){
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        $validate= Validator::make($params_array,[
            'id'=>'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user_update = User::where('id', $params->id)->update($params_array);

        if($validate->fails()){    
            $update = array(
                'status'=>'error',
                'code'=> 400,
                'message'=> 'User not correctly updated',
                'errors'=> $validate->errors()                
            );
        }else{            
            $update = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> $user_update,
                'data' =>$params_array             
            );
        }
        return response()->json($update, $update['code']);
    }
}
