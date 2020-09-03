<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Car;

class CarController extends Controller
{
    //public function index(Request $request){
/*      $hash = $request->header('Authorization',null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            echo "Index de CarController AUTENTICADO";
            die();
        }else{
            echo "Index de CarController NO AUTENTICADO";
            die();
        }//fin if del checkToken */
    public function index(Request $request){
    $cars=Car::all()->load('user');
    return response()->json(array(
            'cars'=>$cars,
            'status'=>'success'
        ),200);
        
    }//fin funcion index

    public function show($id){
        
        $car=Car::find($id)->load('user');
        return response()->json(array(
                    'car'=>$car,
                    'status'=>'success'
                ),200);
        
                
    }//fin funcion show

    public function store(Request $request){
        $hash = $request->header('Authorization',null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            //Guardar Coche
            //Primero Recoger datos por POST
            $json = $request ->input('json',null);
            $params = json_decode($json);
            $params_array = json_decode($json,true); //necesito que sea array para usar el validate
            //Conseguir el usuario identificado
            $user = $jwtAuth->checkToken($hash, true);
            //validacion de los datos params
/*             $request -> merge($params_array);
            try{
                $validate = $this->validate($request,[
                    'title' =>'required|min:5',
                    'description' =>'required',
                    'price' =>'required',
                    'status' =>'required'
                ]);//fin del validatedData
                //var_dump($validate);
                //die();
            }catch(\Illuminate\Validation\ValidationException $e){
                return $e->errors();
            } */
            
            $validate = \Validator::make($params_array,[
                'title' =>'required|min:5',
                'description' =>'required',
                'price' =>'required',
                'status' =>'required'
            ]);//fin del validate
            if($validate->fails()){
                return response()->json($validate->errors(),400);
            }
           
            


            //Guardar el Coche
            $car = new Car();
            $car->user_id = $user->sub;
            $car->title = $params->title;
            $car->description = $params->description;
            $car->price = $params->price;
            $car->status = $params->status;

            $car->save();

            $data = array(
                'car'=>$car,
                'status'=>'success',
                'code'=>200
            );//find de data
        }else{
            //Devolver error
            $data = array(
                'message'=>'Login incorrecto',
                'status'=>'error',
                'code'=>300
            );//find de data

        }//fin if del checkToken

        return response()->json($data, 200);
        
    }//fin funcion store

    public function update($id,Request $request){
        $hash = $request->header('Authorization',null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
            //Guardar Coche
            //Primero Recoger datos por POST
            $json = $request ->input('json',null);
            $params = json_decode($json);
            $params_array = json_decode($json,true); //necesito que sea array para usar el validate
            //Conseguir el usuario identificado
           //creo que no lo usa ahora $user = $jwtAuth->checkToken($hash, true);
            //validacion de los datos params
            $validate = \Validator::make($params_array,[
                'title' =>'required|min:5',
                'description' =>'required',
                'price' =>'required',
                'status' =>'required'
            ]);//fin del validate
            if($validate->fails()){
                return response()->json($validate->errors(),400);
            }

            //Actualiza el auto
            $car = Car::where('id',$id)->update($params_array);

            $data = array(
                //'car'=>$car, no sirve
                'car'=>$params,
                'status'=>'success',
                'code'=>200
            );//find de data
        }else{
            //Devolver error
            $data = array(
                'message'=>'Login incorrecto',
                'status'=>'error',
                'code'=>300
            );//find de data

        }//fin if del checkToken

        return response()->json($data, 200);
        
    }//fin funcion update
    
    public function destroy($id,Request $request){
        $hash = $request->header('Authorization',null);
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){

            //Compruebo que exista el auto
            $car = Car::find($id);
            //Borrarlo
            $car->delete();
            //devolverlo
            $data = array(
                'car'=>$car,
                'status'=>'success',
                'code'=>200
            );//find de data
        }else{
            //Devolver error
            $data = array(
                'message'=>'Login incorrecto',
                'status'=>'error',
                'code'=>400
            );//find de data

        }//fin if del checkToken

        return response()->json($data, 200);
        
    }//fin funcion destroy
}//fin clase
