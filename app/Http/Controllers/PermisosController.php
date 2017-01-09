<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Funciones
use App\libs\Funciones;
//Extras
use config;
use DB;
use \Firebase\JWT\JWT;

class PermisosController extends Controller
{
    public function __construct(Request $request){
        // Funciones
        $this->funciones=new Funciones();
        //Autenticacion
        $key=config('jwt.secret');
        $decoded = JWT::decode($request->token, $key, array('HS256'));
        $this->user=$decoded;
    }

    public function Get_Permisos(Request $request)
    {
        $array=DB::table('usuarios.menu_inicio')->where('id_padre',0)->get();
        $id_padre=$array[0]->id;
        //DASH
        foreach ($array as $key => $value) {
            //$value->chi=ldren=[];
            $hijos=DB::table('usuarios.menu_inicio')->where('id_padre',$value->id)->get();
                
                $value->children=$hijos;
                //HIJOS 2
                foreach ($value->children as $key => $value) {
                     $hijos=DB::table('usuarios.menu_inicio')->where('id_padre',$value->id)->get();
                if (count($hijos)>0) {
                    
                    $value->children=$hijos;
                    //HIJOS 3
                foreach ($value->children as $key => $value) {
                     $hijos=DB::table('usuarios.menu_inicio')->where('id_padre',$value->id)->get();
                if (count($hijos)>0) {
                    
                    $value->children=$hijos;
                    //HIJOS 4
                    foreach ($value->children as $key => $value) {
                     $hijos=DB::table('usuarios.menu_inicio')->where('id_padre',$value->id)->get();
                if (count($hijos)>0) {
                    
                    $value->children=$hijos;
                }else{
                    
                    $value->children=[];
                }
                }
                }else{
                    
                    $value->children=[];
                }
                }
                }else{
                    
                    $value->children=[];
                }
                
                }
            
        }
       return response()->json(['respuesta' =>true,"menu_inicio"=> $array], 200); 
    }

    public function Gen_Permisos_Admin(Request $request){
    	
    	$menu_inicio=[
			['label'=>'INVENTARIO','icon'=>'/images/modulos/inventario.png','path'=>'Dash/Inicio','iconmdi'=>'mdi-archive','color'=>'tc-indigo-900','children'=>
				[
					// ['label'=>'Tipos de Consumo','path'=>'Dash/Inventario/Tipo-Consumo'],
					['label'=>'Categorias','path'=>'Dash/Inventario/Categorias','icon'=>'mdi-chevron-double-right','color'=>'tc-indigo-900','iconmdi'=>'mdi-format-list-bulleted-type','color'=>'tc-indigo-900','children'=>[]],
					['label'=>'Marcas','path'=>'Dash/Inventario/Marcas','icon'=>'mdi-chevron-double-right','color'=>'tc-indigo-900','iconmdi'=>'mdi-animation','color'=>'tc-indigo-900','children'=>[]],
					['label'=>'Modelos','path'=>'Dash/Inventario/Modelos','icon'=>'mdi-chevron-double-right','color'=>'tc-indigo-900','iconmdi'=>'mdi-bandcamp','color'=>'tc-indigo-900','children'=>[]],
					['label'=>'Proveedores','path'=>'Dash/Inventario/Proveedores','icon'=>'mdi-chevron-double-right','color'=>'tc-indigo-900','iconmdi'=>'mdi-clipboard-account','color'=>'tc-indigo-900','children'=>[]],
					['label'=>'Productos','path'=>'Dash/Inventario/Productos','icon'=>'mdi-chevron-double-right','color'=>'tc-indigo-900','iconmdi'=>'mdi-dropbox','color'=>'tc-indigo-900','children'=>[]]
				]
			],
			['label'=>'FACTURACIÓN','icon'=>'/images/modulos/facturacion.png','path'=>'Dash/Inicio','iconmdi'=>'mdi-file-document','color'=>'tc-indigo-900','children'=>
				[
					['label'=>'Vender','path'=>'Dash/Facturacion/Vender','icon'=>'mdi-cart','color'=>'tc-orange-500','iconmdi'=>'mdi-cart','color'=>'tc-orange-500','children'=>[]],
					['label'=>'Clientes','path'=>'Dash/Facturacion/Clientes','icon'=>'mdi-chevron-double-right','color'=>'tc-indigo-900','iconmdi'=>'mdi-account-multiple','color'=>'tc-indigo-900','children'=>[]],
					['label'=>'Mis Facturas','path'=>'Dash/Facturacion/MisFacturas','icon'=>'mdi-file','color'=>'tc-indigo-900','iconmdi'=>'mdi-file-document','color'=>'tc-indigo-900','children'=>[]]
				]
			],
			['label'=>'PROFORMAS','icon'=>'/images/modulos/proformas.png','path'=>'Dash/Proformas','iconmdi'=>'mdi-file-document-box','color'=>'tc-indigo-900','children'=>[]],
			['label'=>'CONFIGURACIÓN','icon'=>'/images/modulos/config.png','path'=>'Dash/Configuracion','iconmdi'=>'mdi-settings','color'=>'tc-indigo-900','children'=>
			[
				// {'label'=>'Usuarios','path'=>'Dash/Inventario/Usuarios'],
				// {'label'=>'Tipos de Usuario','path'=>'Dash/Inventario/Tipos-Usuario']
			]]
								];
								DB::table('usuarios.menu_inicio')->delete();
		//DASH
        foreach ($menu_inicio as $key => $value) {
    DB::table('usuarios.menu_inicio')
    ->insert([
         'label'=>$value['label'],
        'path'=>$value['path'],
        'icon'=>$value['icon'],
        'iconmdi'=>$value['iconmdi'],
        'color'=>$value['color'],         
         'id_padre'=>0,
         ]);
    
    $id_padre=DB::table('usuarios.menu_inicio')->select('id')->where('label',$value['label'])->first();

        foreach ($value['children'] as $key => $value) {
    
            DB::table('usuarios.menu_inicio')
                ->insert([
                     'label'=>$value['label'],
                    'path'=>$value['path'],
                    'icon'=>$value['icon'],
                    'iconmdi'=>$value['iconmdi'],
                    'color'=>$value['color'],                     
                     'id_padre'=>$id_padre->id,
                     ]);
                $id_padre_2=DB::table('usuarios.menu_inicio')->select('id')->where('label',$value['label'])->first();
            if (count($value['children'])>0) {
                foreach ($value['children'] as $key => $value) {
                    
                    if (count($value['children'])>0) {
        
                DB::table('usuarios.menu_inicio')
                    ->insert([
                     'label'=>$value['label'],
                    'path'=>$value['path'],
                    'icon'=>$value['icon'],
                    'iconmdi'=>$value['iconmdi'],
                    'color'=>$value['color'],                     
                     'id_padre'=>$id_padre_2->id,
           
                     ]);
                    $id_padre_3=DB::table('usuarios.menu_inicio')->select('id')->where('label',$value['label'])->first();
                        foreach ($value['children'] as $key => $value) {
        
                    DB::table('usuarios.menu_inicio')
                ->insert([
                     'label'=>$value['label'],
                    'path'=>$value['path'],
                    'icon'=>$value['icon'],
                    'iconmdi'=>$value['iconmdi'],
                    'color'=>$value['color'],                     
                     'id_padre'=>$id_padre_3->id,
           
                     ]);
                    /*$id_vista=DB::table('usuarios.menu_inicio')->select('id')->where('label',$value['label'])->first();
                    DB::table('administracion.usuarios_privilegios')
                        ->insert([                         
                        'id_vista'=>$id_vista->id,
                        'id_tipo_usuario'=>1,
               
                         ]);*/
                        }
                        
                    }
                }
            }

        }
    }

    return response()->json(['respuesta' => true], 200); 

    }
}