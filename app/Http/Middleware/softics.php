<?php

namespace App\Http\Middleware;

use Closure;

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

class softics
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try {

            $licencia = DB::table('system.licencia')->first();
              // Autenticacion
            $key=config('jwt.secret');
            $decoded = JWT::decode($licencia->key, $key, array('HS256'));
            $data_licencia=$decoded;

            // $now_system=new Carbon($data_licencia->fecha_inicio);
            // $now_system=strtotime($now_system->toDateString());
            $now = time();

            // if ($now_system==$now) {
                $date = $data_licencia->fecha_inicio; 

                if (strtotime($date) > $now) {
                   return response()->json(['respuesta' => false],401);
                }

                $fecha_fin = new Carbon($data_licencia->fecha_fin);
                $fecha_actual = Carbon::now();
                $dias_restantes = $fecha_fin->diff($fecha_actual)->days;

                if ($dias_restantes>0) {
                    return $next($request);
                }
                else{
                    return response()->json(['respuesta' => false],401);
                }
            // }else{
            //     return response()->json(['respuesta' => false],401);
            // }
           
        }
        catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json(['respuesta' => $e->getMessage()],401);
            die();
        }

        
    }
}
