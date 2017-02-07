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
use Storage;

class ProformasController extends Controller
{
    public function __construct(Request $request){
        try {
                    // Funciones
        $this->funciones=new Funciones();
        //Autenticacion
        $key=config('jwt.secret');
        $decoded = JWT::decode($request->token, $key, array('HS256'));
        $this->user=$decoded;
        //Paths
        $this->pathLocal  = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
        }
        catch (\Firebase\JWT\ExpiredException $e) {
        return response()->json(['respuesta' => $e->getMessage()]);
        die();
        }
    }

    public function Existencia_Proformas(Request $request)
    {
    $datos=DB::table('facturacion_proformas.proformas')->where('nombre',$request->nombre)->first();
        if (count($datos)==0) {
            return response()->json(['respuesta' => true], 200);
        }else{
            return response()->json(['respuesta' => false], 200);
        }
    }

  public function Add_Proformas(Request $request)
    {
    	$cliente=$request->input('cliente');
    	$detalles=$request->input('detalles');
    	$totales=$request->input('totales');
    	$id_fac=$this->funciones->generarID();

    	$id_cliente=($cliente['telefono']=="999999")?"":$cliente['id'];
    	$datos_serie=DB::table('facturacion_proformas.proformas')->select('serie')->where('tipo_registro',"PROF")->first();
    	$serie=(count($datos_serie)>0)? str_pad((integer)explode('-', $datos_serie->serie)[1]+1, 8, '0', STR_PAD_LEFT): str_pad(1, 8, '0', STR_PAD_LEFT);

    	//return response()->json(['respuesta' =>$serie], 200);

    DB::table('facturacion_proformas.proformas')->insert(
    	[
    	   'id'=>$id_fac,
    	   'id_cliente'=>$id_cliente,
    	   'id_usuario'=>$this->user->id,
           'nombre_proforma'=>$cliente['nombre_proforma'],
    	   // 'forma_pago'=>$cliente->,
    	   'serie'=>'PROF-'.$serie,
	       'subtotal'=>$totales[0]['valor']+$totales[1]['valor'],
	       // 'descuento'=>$cliente->,
	       'base_imponible'=>$totales[3]['valor'],
	       'iva'=>$totales[3]['valor'],
	       'total_pagar'=>$totales[5]['valor'],
	       'estado'=>'A',
	       'tipo_save_fac'=>$cliente['tipo_save_fac'],
           'tipo_registro'=>$cliente['tipo_registro']
    	 ]);

    $last_fac=DB::table('facturacion_proformas.proformas')->where('id',$id_fac)->first();

    foreach ($detalles as $key => $value) {

    	DB::table('facturacion_proformas.detalle_proforma')->insert([
    		'id_proforma'=>$last_fac->id,
    		'codigo_prod'=>$value['codigo_prod'],
    		'descripcion'=>$value['nombre_corto'],
    		'cantidad'=>$value['cantidad_fac'],
    		'valor_unitario'=>$value['precio'],
	        'total_venta'=>$value['total_fac'],
	        'estado'=>'A',
    		]);
    }
    $empresa_data=DB::table('public.datos_empresa')->first();
    $empresa=['ruc_ci'=>$empresa_data->ruc,'nombre'=>$empresa_data->nombre,'direccion'=>$empresa_data->direccion];
    $generacion=$this->generar_pdf($cliente,$detalles,$totales,$last_fac,$empresa);
        

    return response()->json(['respuesta' =>true,'fac'=>$generacion], 200);
    }


    public function generar_pdf($cliente,$detalles,$totales,$last_fac,$empresa) 
    {
        $iddocumento=$last_fac->id;
        $factura=$last_fac;
        $datos=['factura'=>$last_fac,'cliente'=>$cliente,'detalles'=>$detalles,'totales'=>$totales,'empresa'=>$empresa];

        if (!File::exists($this->pathLocal.'/proformas/'.$iddocumento.'.pdf'))
        {
        $data = $datos;
        $date = date('Y-m-d');
        $invoice = "2222";
        $view =  \View::make('invoice_proforma', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->save(public_path().'/proformas/'.$iddocumento.'.pdf');
        }
        return config('global.dir_server').'/public/proformas/'.$iddocumento.'.pdf';
    }

    public function generar_pdf_factura($cliente,$detalles,$totales,$last_fac,$empresa) 
    {
        $iddocumento=$last_fac->id;
        $factura=$last_fac;
        $datos=['factura'=>$last_fac,'cliente'=>$cliente,'detalles'=>$detalles,'totales'=>$totales,'empresa'=>$empresa];

        if (!File::exists($this->pathLocal.'/facturas/'.$iddocumento.'.pdf'))
        {
        $data = $datos;
        $date = date('Y-m-d');
        $invoice = "2222";
        $view =  \View::make('invoice', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->save(public_path().'/facturas/'.$iddocumento.'.pdf');
        }
        return config('global.dir_server').'/public/facturas/'.$iddocumento.'.pdf';
    }

    public function Get_Proformas(Request $request)
    {
    $currentPage = $request->pagina_actual;
    $limit = $request->limit;

    if ($request->has('filter')&&$request->filter!='') {
        $data=DB::table('facturacion_proformas.proformas')
                                                ->where('serie','LIKE','%'.$request->input('filter').'%')
                                                ->where('estado','A')->where('tipo_save_fac',TRUE)->where('tipo_registro','PROF')->orderBy('id','DESC')->get();
    }else{
        $data=DB::table('facturacion_proformas.proformas')->where('estado','A')->where('tipo_registro','PROF')->where('tipo_save_fac',TRUE)->orderBy('id','DESC')->get();
    }

    foreach ($data as $key => $value) {
        $data_cliente=DB::table('facturacion_proformas.clientes')->where('estado','A')->where('id',(integer)$value->id_cliente)->first();
        if (count($data_cliente)>0) {
            $value->cliente=$data_cliente;
        }
        $data_productos=DB::table('facturacion_proformas.detalle_proforma')->where('id_proforma',$value->id)->get();

        foreach ($data_productos as $key_producto => $producto) {
            $data_productos[$key_producto]=DB::table('inventario.productos')->where('codigo_prod',$producto->codigo_prod)->first();
            $data_productos[$key_producto]->cantidad_fac=$producto->cantidad;
            $data_productos[$key_producto]->total_fac=$producto->total_venta;
            $data_productos[$key_producto]->descripcion_corta=$producto->descripcion;
        }
        $value->productos=$data_productos;
    }

    $data=$this->funciones->paginarDatos($data,$currentPage,$limit);
    return response()->json(['respuesta' => $data], 200);
    }

    public function Update_Proformas(Request $request)
    {
    
        $cliente=$request->input('cliente');
        $detalles=$request->input('detalles');
        $totales=$request->input('totales');
        $id_fac=$this->funciones->generarID();

        $id_cliente=($cliente['telefono']=="999999")?"":$cliente['id'];
        $datos_serie=DB::table('facturacion_proformas.factura_venta')->select('serie')->where('tipo_registro',"FAC")->first();
        $serie=(count($datos_serie)>0)? str_pad((integer)explode('-', $datos_serie->serie)[2]+1, 8, '0', STR_PAD_LEFT): str_pad(1, 8, '0', STR_PAD_LEFT);
        // DB::table('facturacion_proformas.detalle_proforma')->where('id_proforma',$cliente['id_proforma'])->delete();
        // DB::table('facturacion_proformas.proformas')->where('id',$cliente['id_proforma'])->delete();
        
        //return response()->json(['respuesta' =>$serie], 200);

    DB::table('facturacion_proformas.factura_venta')->insert(
        [
           'id'=>$id_fac,
           'id_cliente'=>$id_cliente,
           'id_usuario'=>$this->user->id,
           // 'forma_pago'=>$cliente->,
           'serie'=>'001-001-'.$serie,
           'subtotal'=>$totales[0]['valor']+$totales[1]['valor'],
           // 'descuento'=>$cliente->,
           'base_imponible'=>$totales[3]['valor'],
           'iva'=>$totales[3]['valor'],
           'total_pagar'=>$totales[5]['valor'],
           'estado'=>'A',
           'tipo_save_fac'=>$cliente['tipo_save_fac'],
           'tipo_registro'=>$cliente['tipo_registro']
         ]);

    $last_fac=DB::table('facturacion_proformas.factura_venta')->where('id',$id_fac)->first();

    foreach ($detalles as $key => $value) {

        DB::table('facturacion_proformas.detalle_factura_venta')->insert([
            'id_factura_venta'=>$last_fac->id,
            'codigo_prod'=>$value['codigo_prod'],
            'descripcion'=>$value['nombre_corto'],
            'cantidad'=>$value['cantidad_fac'],
            'valor_unitario'=>$value['precio'],
            'total_venta'=>$value['total_fac'],
            'estado'=>'A',
            ]);
    }
    $empresa=['ruc_ci'=>'XXXXXXX RUC','nombre'=>'ABCD','direccion'=>'direccion-----------'];
    $generacion=$this->generar_pdf_factura($cliente,$detalles,$totales,$last_fac,$empresa);
        

    return response()->json(['respuesta' =>true,'fac'=>$generacion], 200);
        
    }

    public function Facturar_Proformas(Request $request)
    {
    $data=DB::table('facturacion_proformas.proformas')->where('id',$request->id)->update(['tipo_registro' => 'FAC' ]);
    return response()->json(['respuesta' => true], 200);
    }

    public function Delete_Proformas(Request $request)
    {
    $data=DB::table('facturacion_proformas.proformas')->where('id',$request->id)->update(['estado'=>'I']);
    return response()->json(['respuesta' => true], 200);
    }
}
