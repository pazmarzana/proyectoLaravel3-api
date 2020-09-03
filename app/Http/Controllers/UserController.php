<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Helpers\JwtAuth;

class UserController extends Controller
{
    //para crear al usuario (chequea si existe, y devuelve la data del usuario)
    public function register(Request $request){
        //Recoger post
        //nos va a llegar un json con la informacion
        $json = $request ->input('json',null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $role = 'ROLE_USER';
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        
        if(!is_null($email) && !is_null($password) && !is_null($name)){
            //crear el usuario
            $user = new User();
            $user->email = $email;
            $user->name = $name;
            $user->surname = $surname;
            $user->role = $role;

            //$user->password = $password; NO HAY QUE HACERLO ASI hay que encriptarla
            $pwd = hash('sha256', $password);
            $user->password = $pwd;

            //comprobar si usuario duplicado
            $isset_user = User::where('email','=', $email)->first();
            if(count((array)$isset_user) == 0){
                //Guardar el usuario
                $user->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message'=> 'Usuario creado exitosamente'
                );

            }else{
                //No guardar el usuario porque ya existe
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message'=> 'Usuario duplicado, no puede registrarse nuevamente'
                );

            }


        }else{
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message'=> 'Usuario no creado'
            );
        }

        return response()->json($data, 200);
        
    }

    //chequea si los datos son correctos para entrar utiliza la funcion que hicimos signup
    public function login(Request $request){
        $jwtAuth = new JwtAuth();
        //recibir los datos por post
        $json = $request->input('json',null);
        $params = json_decode($json);
        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;
        //$getToken = true; //sacar esto

        //cifrar la password

        $pwd=hash('sha256',$password);
        //primero hizo lo siguiente y despues lo modifico
/*         if(!is_null($email) && !is_null($password)){
            $signup = $jwtAuth->signup($email,$pwd);
        return response()->json($signup,200);
        } */
        
        if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')){
            $signup = $jwtAuth->signup($email, $pwd);
        }elseif($getToken != null){
            //var_dump($getToken);die();
            $signup = $jwtAuth->signup($email, $pwd, $getToken);
        }else{
            //var_dump($getToken);die();
            $signup = array(
                'status' => 'error',
                'message' => 'Envia tus datos por post'
            );
        }//fin if
        return response()->json($signup,200);
    }//fin funcion login
}//fin clase
