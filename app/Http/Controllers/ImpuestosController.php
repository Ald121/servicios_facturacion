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

class ImpuestosController extends Controller
{
    public function __construct(Request $request){
        try {
            // Funciones
            $this->funciones=new Funciones();
            //Autenticacion
            $key=config('jwt.secret');
            $decoded = JWT::decode($request->token, $key, array('HS256'));
            $this->user=$decoded;
        }
        catch (\Firebase\JWT\ExpiredException $e) {
        return response()->json(['respuesta' => $e->getMessage()]);
        die();
        }
    }

    public function Existencia_Impuestos(Request $request)
    {
    $datos=DB::table('inventario.impuestos')->where('nombre',$request->nombre)->first();
        if (count($datos)==0) {
            return response()->json(['respuesta' => true], 200);
        }else{
            return response()->json(['respuesta' => false], 200);
        }
    }

  public function Add_Impuestos(Request $request)
    {
    DB::table('inventario.impuestos')->insert(['nombre' => $request->nombre, 'porcentaje' => $request->porcentaje, 'estado' => 'A']);
    return response()->json(['respuesta' => true], 200);
    }

    public function Get_Impuestos(Request $request)
    {
    $currentPage = $request->pagina_actual;
    $limit = $request->limit;

    if ($request->has('filter')&&$request->filter!='') {
        $data=DB::table('inventario.impuestos')
                                                ->where('nombre','LIKE','%'.$request->input('filter').'%')
                                                ->where('estado','A')->orderBy('nombre','ASC')->get();
    }else{
        $data=DB::table('inventario.impuestos')->where('estado','A')->orderBy('nombre','ASC')->get();
    }
    $data=$this->funciones->paginarDatos($data,$currentPage,$limit);
    return response()->json(['respuesta' => $data], 200);
    }

    public function Update_Impuestos(Request $request)
    {
    $data=DB::table('inventario.impuestos')->where('id',$request->id)->update(['nombre' => $request->nombre , 'descripcion' => $request->descripcion]);
    return response()->json(['respuesta' => true], 200);
    }

    public function Delete_Impuestos(Request $request)
    {
    $data=DB::table('inventario.impuestos')->where('id',$request->id)->update(['estado'=>'I']);
    return response()->json(['respuesta' => true], 200);
    }
}
