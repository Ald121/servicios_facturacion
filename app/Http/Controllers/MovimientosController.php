<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Funciones
use App\libs\Funciones;
//Extras
use config;
use DB;
use \Firebase\JWT\JWT;

class MovimientosController extends Controller
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

    public function Existencia_Movimientos(Request $request)
    {
    $datos=DB::table('inventario.modelos')->where('nombre',$request->nombre)->first();
        if (count($datos)==0) {
            return response()->json(['respuesta' => true], 200);
        }else{
            return response()->json(['respuesta' => false], 200);
        }
    }

  public function Add_Movimientos(Request $request)
    {
	    DB::table('inventario.movimientos_mercaderia')->insert(
	    	[
	    		'tipo_movimiento'=>$request->tipo_movimiento,
	    		'total_movimiento'=>$request->total_movimiento,
	    		'id_proveedor'=>$request->id_proveedor,
				'estado'=>'A',
				'tipo_documento'=>$request->tipo_documento
	    	]);
	    return response()->json(['respuesta' => true], 200);
    }

    public function Get_Movimientos(Request $request)
    {
    $currentPage = $request->pagina_actual;
    $limit = $request->limit;

    if ($request->has('filter')&&$request->filter!='') {
        $data=DB::table('inventario.movimientos_mercaderia')
                                                ->where('id','LIKE','%'.$request->input('filter').'%')
                                                //->orwhere('descripcion','LIKE','%'.$request->input('filter').'%')
                                                ->where('estado','A')->orderBy('id','ASC')->get();
    }else{
        $data=DB::table('inventario.movimientos_mercaderia')->where('estado','A')->orderBy('id','ASC')->get();
    }
    $data=$this->funciones->paginarDatos($data,$currentPage,$limit);
    return response()->json(['respuesta' => $data], 200);
    }

    public function Update_Movimientos(Request $request)
    {
    $data=DB::table('inventario.movimientos_mercaderia')->where('id',$request->id)->update(['nombre' => $request->nombre , 'descripcion' => $request->descripcion]);
    return response()->json(['respuesta' => true], 200);
    }

    public function Delete_Movimientos(Request $request)
    {
    $data=DB::table('inventario.movimientos_mercaderia')->where('id',$request->id)->update(['estado'=>'I']);
    return response()->json(['respuesta' => true], 200);
    }
}
