<?php
namespace App\Helpers;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{

    public $key;

    public function __construct(){
        $this->key = 'Key_token';
    }

    public function signup($email, $password,$getToken = null){
        
        // Search if user exist
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();
        
        //Validate user
        $signup = false;
        if(isset($user)){
            $signup = true;
        }

        //Generate token
        if($signup==true){
            $token = array(
                'id' => $user->id,
                'email' => $user->email,
                'user_name' => $user->user_name,
                'iat' => time(),
                'exp' => time() + 86400
            );
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decode = JWT::decode($jwt, $this->key, ['HS256']);
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decode;
            }
        }else{
            $data = array(
                'status' => 'error',
                'message' => 'Invalid Login'
            );
        }
        return $data;
    }

    public function checkToken($jwt, $getIdentity = false){
        $auth = false;

        try{
            $jwt = str_replace('"','', $jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        }catch(\UnexpectedValueException $exception){
            $auth = false;
        }catch(\DomainException $exception){
            $auth = false;
        }

        if(isset($decoded) && isset($decoded->id)){
            $auth = true;
        }else{
            $auth = false;
        }
        if($getIdentity){
            return $decoded;
        }
        return $auth;
    }
}