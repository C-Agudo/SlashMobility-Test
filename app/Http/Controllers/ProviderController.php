<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Provider;
use Validator;

class ProviderController extends Controller
{
    public function register(Request $request){
        
        //Catch the request
        $json = $request->input('json');
        $params = json_decode($json);
        $params_array = json_decode($json,true);


        //Validation
        $validate= Validator::make($params_array,[
            'name' =>'required',
            'adress' => 'required',
            'telephone' => 'required',
            'city' => 'required'
        ]);

        if($validate->fails()){
            $responseData = array(
                'status'=>'error',
                'code'=> 404,
                'message'=> 'invalid data',
                'errors'=> $validate->errors()
            );
        }else{
            //Create and save provider
            $provider = new Provider();
            $provider->name = $params_array['name'];
            $provider->adress = $params_array['adress'];
            $provider->telephone = $params_array['telephone'];
            $provider->city = $params_array['city'];

            $provider->save();

            $responseData = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> $provider,
            );
        }
        return response()->json($responseData, $responseData['code']);
    }

    public function list(){
        $providerList = Provider::all();
        if(isset($providerList)){
            $list = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> $providerList,                
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

    public function detail($name, Request $request ){

        $provider = Provider::all()->whereIn('name', [$name]);

        $detail = array(
            'status'=>'succes',
            'code'=> 200,
            'message'=> $provider,
        );
    
    return response()->json($detail, $detail['code']);
    }

    public function update(Request $request){

            $json = $request->input('json', null);
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            $validate= Validator::make($params_array,[
                'id' => 'required',
                'name' =>'required',
                'adress' => 'required',
                'telephone' => 'required',
                'city' => 'required'
            ]);
        
            $provider_update = Provider::where('id', $params->id)->update($params_array);

        if($validate->fails()){    
            $update = array(
                'status'=>'error',
                'code'=> 400,
                'message'=> 'Provider not correctly updated',
                'errors'=> $validate->errors()                
            );
        }else{            
            $update = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> $provider_update,
                'data' =>$params_array             
            );
        }
        return response()->json($update, $update['code']);
    }
}
