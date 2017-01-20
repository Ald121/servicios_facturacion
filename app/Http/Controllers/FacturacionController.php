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

class FacturacionController extends Controller
{
     public function __construct(Request $request){
        // Funciones
        $this->funciones=new Funciones();
        //Autenticacion
        $key=config('jwt.secret');
        $decoded = JWT::decode($request->token, $key, array('HS256'));
        $this->user=$decoded;
    }

    public function Existencia_Facturas(Request $request)
    {
    $datos=DB::table('facturacion_proformas.factura_venta')->where('nombre',$request->nombre)->first();
        if (count($datos)==0) {
            return response()->json(['respuesta' => true], 200);
        }else{
            return response()->json(['respuesta' => false], 200);
        }
    }

  public function Add_Facturas(Request $request)
    {
    	$cliente=$request->input('cliente');
    	$detalles=$request->input('detalles');
    	$totales=$request->input('totales');
    	$id_fac=$this->funciones->generarID();

    DB::table('facturacion_proformas.factura_venta')->insert(
    	[
    	   'id'=>$id_fac,
    	   'id_cliente'=>$cliente['id'],
    	   'id_usuario'=>$this->user->id,
    	   // 'forma_pago'=>$cliente->,
    	   'serie'=>'00000',
	       'subtotal'=>$totales[0]['valor']+$totales[1]['valor'],
	       // 'descuento'=>$cliente->,
	       'base_imponible'=>$totales[3]['valor'],
	       'iva'=>$totales[3]['valor'],
	       'total_pagar'=>$totales[3]['valor'],
	       'estado'=>'A',
	       'tipo_save_fac'=>$cliente->tipo_save_fac
    	 ]
    	);

    return response()->json(['respuesta' => $this->user], 200);
    }

    public function Get_Facturas(Request $request)
    {
    $currentPage = $request->pagina_actual;
    $limit = $request->limit;

    if ($request->has('filter')&&$request->filter!='') {
        $data=DB::table('facturacion_proformas.factura_venta')
                                                ->where('nombre','LIKE','%'.$request->input('filter').'%')
                                                ->where('estado','A')->orderBy('nombre','ASC')->get();
    }else{
        $data=DB::table('facturacion_proformas.factura_venta')->where('estado','A')->orderBy('nombre','ASC')->get();
    }
    $data=$this->funciones->paginarDatos($data,$currentPage,$limit);
    return response()->json(['respuesta' => $data], 200);
    }

    public function Update_Facturas(Request $request)
    {
    $data=DB::table('facturacion_proformas.factura_venta')->where('id',$request->id)->update(['nombre' => $request->nombre , 'descripcion' => $request->descripcion]);
    return response()->json(['respuesta' => true], 200);
    }

    public function Delete_Facturas(Request $request)
    {
    $data=DB::table('facturacion_proformas.factura_venta')->where('id',$request->id)->update(['estado'=>'I']);
    return response()->json(['respuesta' => true], 200);
    }
}
