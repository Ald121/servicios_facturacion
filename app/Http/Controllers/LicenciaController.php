<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Funciones
use App\libs\Funciones;
//Extras
use config;
use DB;
use \Firebase\JWT\JWT;
use Image;
use File;
use Carbon\Carbon;

use JWTFactory;
use JWTAuth;

class LicenciaController extends Controller
{
     public function __construct(Request $request){
            // Funciones
            $this->funciones=new Funciones();
    }

    public function Gen_Licencia(Request $request)
    {

	   $id=$this->funciones->generarID();
	   $datos = DB::table('usuarios.usuarios')->select('id','nick')->where('nick','admin')->first();
	   $extra=['fecha_inicio'=>Carbon::now()->toDateTimeString(),'fecha_fin'=>Carbon::now()->addDays(1)->toDateTimeString(),'key'=>$id];
       $token = JWTAuth::fromUser($datos,$extra);
       $licencia = DB::table('system.licencia')->first();
       if (count($licencia)==0) {
       	$datos = DB::table('system.licencia')->insert(['id'=>$id,'key'=>$token,'estado'=>'A']);
       }
       
	  return response()->json(['respuesta' =>  $extra], 200);
	    
    }

    public function Get_Licencia(Request $request)
    {

       $licencia = DB::table('system.licencia')->first();
        //Autenticacion
            $key=config('jwt.secret');
            $decoded = JWT::decode($licencia->key, $key, array('HS256'));
            $data_licencia=$decoded;
       
	  return response()->json(['respuesta' =>  $data_licencia], 200);
	    
    }
}
