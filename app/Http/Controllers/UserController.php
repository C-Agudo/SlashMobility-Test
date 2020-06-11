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
            $passwordHash = password_hash($params->password, PASSWORD_BCRYPT, ['cost'=>4]);

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
            // Mail::send(new confirmationEmail($user),[], function($message) use ($user){
            //     $message->to($user['email'])
            //     ->subject('Confirm your email')
            //     ->from(env('MAIL_USERNAME'));
            // });
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
}
