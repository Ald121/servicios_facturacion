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

       $licencia = DB::table('system.licencia')->first();
       if (count($licencia)==0) {

          $key = config('jwt.secret');
          $token = array(
              'exp'=>strtotime(Carbon::now()->addYears(1)->toDateTimeString()),
              'fecha_fin'=>Carbon::now()->addYears(1)->toDateTimeString(),
              'fecha_inicio'=>Carbon::now()->toDateTimeString(),
              'iss'=>config('global.dir_server')."/public/Get_Licencia",
              'jti'=>"aeceba384cf7dc5c12c2279f076a1b59",
              'key'=>$id,
              'sub'=>"1"
          );
          $jwt = JWT::encode($token, $key);

       	$datos = DB::table('system.licencia')->insert(['id'=>$id,'key'=>$jwt,'estado'=>'A','fecha_fin'=>Carbon::now()->addYears(1)->toDateTimeString()]);
       }
       
	  return response()->json(['respuesta' =>  true], 200);
	    
    }

    public function Get_Licencia(Request $request)
    {

       try {
            $licencia = DB::table('system.licencia')->first();
              //Autenticacion
            $key=config('jwt.secret');
            $decoded = JWT::decode($licencia->key, $key, array('HS256'));
            $data_licencia=$decoded;

            $created = new Carbon($data_licencia->fecha_fin);
            $now = Carbon::now();
            $dias_restantes = $created->diff($now)->days;
       
            return response()->json(['respuesta' => true,'dias_restantes'=>$dias_restantes], 200);
        }
        catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json(['respuesta' => $e->getMessage()],401);
            die();
        }
	    
    }
}
