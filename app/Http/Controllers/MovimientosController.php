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
        $proveedor=$request->proveedor;
        $detalles=$request->detalles;

        DB::table('inventario.movimientos_mercaderia')->insert(
            [
                'tipo_movimiento'=>$proveedor['tipo_movimiento'],
                'total_movimiento'=>$proveedor['ingreso_total'],
                'id_proveedor'=>$proveedor['id'],
                'estado'=>'A',
                'tipo_documento'=>$proveedor['tipo_documento']
            ]);
        $last_movimiento=DB::table('inventario.movimientos_mercaderia')
        ->where('total_movimiento',$proveedor['ingreso_total'])
        ->where('tipo_movimiento',$proveedor['tipo_movimiento'])
        ->where('tipo_documento',$proveedor['tipo_documento'])->orderBy('id','DESC')
        ->first();

        foreach ($detalles as $key => $value) { 
            DB::table('inventario.detalles_movimientos_mercaderia')->insert(
                [
                    'id_producto'=>$value['id'],
                    'cantidad'=>$value['cantidad_fac'],
                    'id_movimiento'=>$last_movimiento->id
                ]
                );

            $data_prod=DB::table('inventario.productos')->select('cantidad')->where('id',$value['id'])->first();
            $suma=$data_prod->cantidad+$value['cantidad_fac'];
            DB::table('inventario.productos')->where('id',$value['id'])->update(['cantidad'=>$suma]);
        }

	   
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

        foreach ($data as $key => $value) {
            //selecionar Tipo de documento
            $data_tipo_documento=DB::table('public.tipo_documentos')->select('nombre','id')->where('id',$value->tipo_documento)->first();
            $value->tipo_documento=$data_tipo_documento;
            //selecionar Proveedor
            $data_proveedor=DB::table('inventario.proveedores')->select('nombre','id')->where('id',$value->id_proveedor)->first();
            $value->id_proveedor=$data_proveedor;
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
