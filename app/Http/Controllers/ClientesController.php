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

class ClientesController extends Controller
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

    public function Get_By_Ruc_Ci(Request $request)
    {
    $datos=DB::table('facturacion_proformas.clientes')->where('ruc_ci',$request->ruc_ci)->first();
        if (count($datos)==0) {
            return response()->json(['respuesta' => false], 200);
        }else{
            return response()->json(['respuesta' => $datos], 200);
        }
    }

    public function Existencia_Clientes(Request $request)
    {
    $datos=DB::table('facturacion_proformas.clientes')->where('ruc_ci',$request->nombre)->first();
        if (count($datos)==0) {
            return response()->json(['respuesta' => true], 200);
        }else{
            return response()->json(['respuesta' => false], 200);
        }
    }

  public function Add_Clientes(Request $request)
    {
    DB::table('facturacion_proformas.clientes')->insert([
       'ruc_ci'=>$request->ruc_ci,
       'nombre_comercial'=>$request->nombre_comercial,
       'actividad_economica'=>$request->actividad_economica,
       'razon_social'=>$request->nombres.' '.$request->apellidos,
       'representante_legal'=>$request->representante_legal,
       'cedula_representante'=>$request->cedula_representante,
       'celular'=>$request->celular,
       'telefono'=>$request->telefono,
       'direccion'=>$request->direccion,
       'correo'=>$request->correo,
       'observaciones'=>$request->observaciones,
       'estado'=>'A',
       'id_localizacion'=>$request->id_localizacion
    	]);
    return response()->json(['respuesta' => true], 200);
    }

    public function Get_Clientes(Request $request)
    {
    $currentPage = $request->pagina_actual;
    $limit = $request->limit;

    if ($request->has('filter')&&$request->filter!='') {
        $data=DB::table('facturacion_proformas.clientes')
                                                ->where('nombre','LIKE','%'.$request->input('filter').'%')
                                                ->where('estado','A')->orderBy('razon_social','ASC')->get();
    }else{
        $data=DB::table('facturacion_proformas.clientes')->where('estado','A')->orderBy('razon_social','ASC')->get();
    }

    foreach ($data as $key => $value) {
    	$resultado=DB::select("SELECT nombre FROM view_localizacion WHERE length(id_padre)=2 and nombre!='ECUADOR' and id='".$value->id_localizacion."' ORDER BY nombre ASC");
    	$value->nombre_localizacion=$resultado[0]->nombre;
    }

    $data=$this->funciones->paginarDatos($data,$currentPage,$limit);
    return response()->json(['respuesta' => $data], 200);
    }

    public function Update_Clientes(Request $request)
    {
    $data=DB::table('facturacion_proformas.clientes')->where('id',$request->id)->update(['nombre' => $request->nombre , 'descripcion' => $request->descripcion]);
    return response()->json(['respuesta' => true], 200);
    }

    public function Delete_Clientes(Request $request)
    {
    $data=DB::table('facturacion_proformas.clientes')->where('id',$request->id)->update(['estado'=>'I']);
    return response()->json(['respuesta' => true], 200);
    }
}
