<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Funciones
use App\libs\Funciones;
//Extras
use config;
use DB;
use \Firebase\JWT\JWT;
use Carbon\Carbon;
use Date;

class ReportesController extends Controller
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

    public function Get_Prods_Mas_Vendidos(Request $request)
    {
	    $productos=DB::select("SELECT codigo_prod,SUM(cast(cantidad as int)) as suma FROM facturacion_proformas.detalle_factura_venta WHERE estado='A' GROUP BY codigo_prod order By suma DESC LIMIT 5;");
	    $labels=[];
	    $sumas=[];

	    foreach ($productos as $key => $value) {
	    	 $prod=DB::table('inventario.productos')->select('id','nombre_corto','codigo_prod','unidad')->where('codigo_prod',$value->codigo_prod)->first();
	    	 // $unidad=DB::table('inventario.unidades')->select('nombre','id')->where('id',$prod->unidad)->first();
	    	 // $value->producto=$prod;
	    	 // $value->unidad=$unidad;
	    	 $labels[$key]=$prod->nombre_corto;
	    	 $sumas[$key]=$value->suma;
      	// 	 if ($value->unidad->id==4) {
	    	 // 	$value->suma=$value->suma*12;
	    	 // }

	    }

	    return response()->json(['respuesta' => true,'labels'=>$labels,'sumas'=>$sumas], 200);

    }

    public function Get_Ventas_X_Mes(Request $request)
    {   
        $mes_request=$request->mes;
        //return response()->json(['respuesta' => $mes_request['nombre']], 200);

        if ($mes_request['nombre']=='TODOS') {
        $ventas=DB::select("SELECT count(*)as nro_ventas,date_trunc( 'month', fecha_creacion ) AS mes FROM facturacion_proformas.factura_venta GROUP BY date_trunc( 'month', fecha_creacion ) ORDER BY nro_ventas DESC;");
        $labels=['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];
        $sumas=[0,0,0,0,0,0,0,0,0,0,0,0];

        foreach ($ventas as $key => $venta) {
            Carbon::setLocale(config('app.locale'));
            $mes= new Date($venta->mes);
            $mes=$mes->format('F');
            foreach ($labels as $key => $value) {
                if (strtoupper($mes)==$value) {
                    $sumas[$key]=(integer)$venta->nro_ventas;
                }
            }
        }
        return response()->json(['respuesta' => true,'labels'=>$labels,'sumas'=>$sumas], 200);
        }else{
            
            $ventas=DB::select("SELECT count(*)as nro_ventas,date_trunc( 'day', fecha_creacion ) AS fecha_creacion FROM facturacion_proformas.factura_venta GROUP BY date_trunc( 'day', fecha_creacion ) ORDER BY nro_ventas DESC;");
            $a=0;
            $sumas=[];
            $labels=[];

            foreach ($ventas as $key => $value) {
                $fecha_venta= new Date($value->fecha_creacion);
                $fecha_mes=$fecha_venta->month;
                if ($fecha_mes==$mes_request['id']) {
                    $sumas[$a]=$fecha_venta->day;
                    $labels[$a]=$fecha_venta->toDateString();
                    $a++;
                }
                
            }
            return response()->json(['respuesta' => true,'labels'=>$labels,'sumas'=>$sumas], 200);
        }

    }

  
}
