<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Hash;
//------------------------------------ Autenticacion --------------------
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class loginController extends Controller
{
    public function __construct(){

    }

    public function Acceso(Request $request){
         //return response()->json(["respuesta"=>$request->all()]);
        $datos=DB::table('usuarios.usuarios')->select('nick','id')->where('nick',$request->nick)->first();

        if (count($datos)==0) {
            return response()->json(["respuesta"=>false]);
        }

        $datos=DB::table('usuarios.usuarios')->select('clave_clave')->where('id',$datos->id)->first();
        $checkpass=Hash::check($request->clave, $datos->clave_clave);

        if ($checkpass) {
         $datos = DB::table('usuarios.usuarios')->select('id','nick')->where('nick',$request->nick)->first();
         // $extra=['nbdb'=>$name_bdd,'pnb'=>$pass_bdd,'ruc'=>$acceso->nick];
         // $token = JWTAuth::fromUser($datos,$extra);
         $token = JWTAuth::fromUser($datos);
         $datosUser=DB::table('usuarios.usuarios')->select('id','nick')->where('nick',$request->nick)->first();
         //DB::table('usuarios.usuarios')->where('nick',$user)->update(["token"=>$token]);
         return response()->json(['respuesta'=>true,'token'=>$token,'datosUser'=>$datosUser]);
        }
        return response()->json(["respuesta"=>$checkpass]);
        //return response()->json(["respuesta"=>$name_bdd]);
           
    }

    public function Salir(Request $request){
         
         $token = JWTAuth::getToken();
        if ($token) {
            JWTAuth::setToken($token)->invalidate();
            return response()->json(["respuesta"=>true]);
        }
        return response()->json(["respuesta"=>false]);
        //return response()->json(["respuesta"=>$name_bdd]);
           
    }
}
