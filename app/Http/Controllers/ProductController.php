<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Validator;

class ProductController extends Controller
{
    public function register(Request $request){
        
        //Catch the request
        $json = $request->input('json');
        $params = json_decode($json);
        $params_array = json_decode($json,true);


        //Validation
        $validate= Validator::make($params_array,[
            'provider_id' =>'required',
            'name' =>'required',
            'type' => 'required',
            'description' => 'required',
            'image' => 'required'
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
            $product = new Product();
            $product->provider_id = $params_array['provider_id'];
            $product->name = $params_array['name'];
            $product->type = $params_array['type'];
            $product->description = $params_array['description'];
            $product->image = $params_array['image'];

            $product->save();

            $responseData = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> $product,
            );
        }
        return response()->json($responseData, $responseData['code']);
    }

    public function list(){
        $productList = Product::all();
        if(isset($productList)){
            $list = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> $productList,                
            );
        }else{
            $list = array(
                'status'=>'error',
                'code'=> 400,
                'message'=> 'Product not found',                
            );
        }
        return response()->json($list, $list['code']);
    }

    public function detail($name, Request $request ){

        $product = Product::all()->whereIn('name', [$name]);

        $detail = array(
            'status'=>'succes',
            'code'=> 200,
            'message'=> $product,
        );
    
    return response()->json($detail, $detail['code']);
    }

    public function update(Request $request){

        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        $validate = Validator::make($params_array,[
            'id' => 'required',
            'provider_id' =>'required',
            'name' => 'required',
            'type' => 'required',
            'description' => 'required',
            'image' => 'required'
        ]);
    
        $provider_update = Product::where('id', $params->id)->update($params_array);

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

    public function listType($type){
        $productList = Product::all()->whereIn('type', [$type]);
        if(isset($productList)){
            $list = array(
                'status'=>'succes',
                'code'=> 200,
                'message'=> $productList,                
            );
        }else{
            $list = array(
                'status'=>'error',
                'code'=> 400,
                'message'=> 'Product not found',                
            );
        }
        return response()->json($list, $list['code']);
    }

}
