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

class localizacionController extends Controller
{
    public function Get_Localizacion(Request $request)
    {
	 $resultado=DB::select("SELECT * FROM view_localizacion WHERE length(id_padre)=2 and nombre!='ECUADOR' ORDER BY nombre ASC");
      return response()->json(["respuesta" => $resultado], 200);
    }
}
