<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Funciones
use App\libs\Funciones;
//Extras
use config;
use DB;
use \Firebase\JWT\JWT;

class ProveedoresController extends Controller
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

    public function Existencia_Proveedores(Request $request)
    {
    $datos=DB::table('inventario.proveedores')->where('ruc',$request->nombre)->first();
        if (count($datos)==0) {
            return response()->json(['respuesta' => true], 200);
        }else{
            return response()->json(['respuesta' => false], 200);
        }
    }

  public function Add_Proveedores(Request $request)
    {
    	$celular=array_key_exists('celular', $request->input('proveedor'))?$request->input('proveedor')['celular']:null;
    	$telefono=array_key_exists('telefono', $request->input('proveedor'))?$request->input('proveedor')['telefono']:null;
    	$correo=array_key_exists('correo', $request->input('proveedor'))?$request->input('proveedor')['correo']:null;
    	
     	$array=['telefono'=>$telefono,'celular'=>$celular,'correo'=>$correo];
     	$json_contacto=json_encode($array);
    	DB::table('inventario.proveedores')->insert([
    	'nombre'=>$request->input('proveedor')['nombre'],
    	'ruc'=>$request->input('proveedor')['ruc'],
    	'direccion'=>$request->input('proveedor')['direccion'],
    	'datos_propietario'=>json_encode($request->input('persona')),
    	'datos_contacto'=>$json_contacto,
    	'estado'=>'A'
    	]);
    return response()->json(['respuesta' => true], 200);
    }

    public function Get_Proveedores(Request $request)
    {
    $currentPage = $request->pagina_actual;
    $limit = $request->limit;

    if ($request->has('filter')&&$request->filter!='') {
        $data=DB::table('inventario.proveedores')
                                                ->where('nombre','LIKE','%'.$request->input('filter').'%')
                                                //->orwhere('descripcion','LIKE','%'.$request->input('filter').'%')
                                                ->where('estado','A')->orderBy('nombre','ASC')->get();
    }else{
        $data=DB::table('inventario.proveedores')->where('estado','A')->orderBy('nombre','ASC')->get();
    }
    foreach ($data as $key => $value) {
    	$value->datos_contacto=json_decode($value->datos_contacto);
    	$value->datos_propietario=json_decode($value->datos_propietario);
    }
    $data=$this->funciones->paginarDatos($data,$currentPage,$limit);
    return response()->json(['respuesta' => $data], 200);
    }

    public function Update_Proveedores(Request $request)
    {
    	$celular=array_key_exists('celular', $request->input('proveedor'))?$request->input('proveedor')['celular']:null;
    	$telefono=array_key_exists('telefono', $request->input('proveedor'))?$request->input('proveedor')['telefono']:null;
    	$correo=array_key_exists('correo', $request->input('proveedor'))?$request->input('proveedor')['correo']:null;
    	
     	$array=['telefono'=>$telefono,'celular'=>$celular,'correo'=>$correo];
     	$json_contacto=json_encode($array);
    	DB::table('inventario.proveedores')->update([
    	'nombre'=>$request->input('proveedor')['nombre'],
    	'ruc'=>$request->input('proveedor')['ruc'],
    	'direccion'=>$request->input('proveedor')['direccion'],
    	'datos_propietario'=>json_encode($request->input('persona')),
    	'datos_contacto'=>$json_contacto
    	]);
    return response()->json(['respuesta' => true], 200);
    }

    public function Delete_Proveedores(Request $request)
    {
    $data=DB::table('inventario.proveedores')->where('id',$request->id)->update(['estado'=>'I']);
    return response()->json(['respuesta' => true], 200);
    }

    public function Get_Proveedor_By_Ruc(Request $request)
    {
    $data=DB::table('inventario.proveedores')->where('ruc',$request->ruc)->first();
    if (count($data)>0) {
        return response()->json(['respuesta' => true,'proveedor'=>$data], 200);
    }else return response()->json(['respuesta' => false], 200);
    
    }
}
