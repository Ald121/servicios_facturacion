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

class InfoEmpresaController extends Controller
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

  public function Add_Informacion(Request $request)
    {
    	$empresa=DB::table('public.datos_empresa')->first();
    if (count($empresa)==0) {
    	    	//Guardar Imagen
	    $img=$request->file('files')[0];
	    $extension=$img->getClientOriginalExtension();
	    $path=public_path().'/empresa/';
	    $nombre_img=$request->ruc.".".$extension;
	    Image::make($img->getRealPath())->save($nombre_img);
	     // Mover Archivo
	    File::move(public_path().'/'.$nombre_img,$path.$nombre_img);

    	DB::table('public.datos_empresa')->insert([
    	'id'=>$this->funciones->generarID(),
    	'nombre'=>$request->nombre,
    	'ruc'=>$request->ruc,
    	'imagen'=>'empresa/'.$nombre_img,
    	'direccion'=>$request->direccion,
    	'telefono'=>$request->telefono,
    	'celular'=>$request->celular,
    	'correo'=>$request->correo
    	]);

    }else{
    	//Guardar Imagen
	    $img=$request->file('files')[0];
	    $extension=$img->getClientOriginalExtension();
	    $path=public_path().'/empresa/';
	    $nombre_img=$request->ruc.".".$extension;
	    Image::make($img->getRealPath())->save($nombre_img);
	     // Mover Archivo
	    File::move(public_path().'/'.$nombre_img,$path.$nombre_img);

    	DB::table('public.datos_empresa')->where('id',$empresa->id)->update([
    	'id'=>$request->nombre,
    	'nombre'=>$request->nombre,
    	'ruc'=>$request->ruc,
    	'imagen'=>'empresa/'.$nombre_img,
    	'direccion'=>$request->direccion,
    	'telefono'=>$request->telefono,
    	'celular'=>$request->celular,
    	'correo'=>$request->correo
    	]);
    }
    
    return response()->json(['respuesta' => true], 200);
    }

    public function Get_Informacion(Request $request)
    {
    $data=DB::table('public.datos_empresa')->first();
    if (count($data)>0) {
    	return response()->json(['respuesta' => $data], 200);
    }else return response()->json(['respuesta' => false], 200);
    
    }

    public function Update_Informacion(Request $request)
    {
    $data=DB::table('public.datos_empresa')->where('id',$request->id)->update(['nombre' => $request->nombre , 'descripcion' => $request->descripcion]);
    return response()->json(['respuesta' => true], 200);
    }

    public function Delete_Informacion(Request $request)
    {
    $data=DB::table('public.datos_empresa')->where('id',$request->id)->update(['estado'=>'I']);
    return response()->json(['respuesta' => true], 200);
    }
}
