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

class ProductosController extends Controller
{
    public function __construct(Request $request){
        // Funciones
        $this->funciones=new Funciones();
        //Autenticacion
        $key=config('jwt.secret');
        $decoded = JWT::decode($request->token, $key, array('HS256'));
        $this->user=$decoded;
    }

    public function Existencia_Productos(Request $request)
    {
    $datos=DB::table('inventario.productos')->where('nombre_corto',$request->nombre)->first();

   // return response()->json(['respuesta' => count($datos)], 200);
        if (count($datos)==0) {
            return response()->json(['respuesta' => true], 200);
        }else{
            return response()->json(['respuesta' => false], 200);
        }
    }

  public function Add_Productos(Request $request)
    {
    //Guardar Descripcion
    DB::table('inventario.descripcion_producto')->insert([
    'descripcion_corta'=>$request->descripcion_corta,
    'descripcion_proveedor'=>$request->descripcion_proveedor,
    'descripcion_proformas'=>$request->descripcion_proformas
    ]);
    $datos_descripcion_prod=DB::table('inventario.descripcion_producto')->where('descripcion_corta',$request->descripcion_corta)->first();
        //Guardar Producto
    DB::table('inventario.productos')->insert([
        'nombre_corto'=>$request->nombre_corto,
        'precio'=>$request->precio,
        'costo'=>$request->costo,
        'categoria'=>$request->categoria,
        'marca'=>$request->marca,
        'modelo'=>$request->modelo,
        'cantidad'=>$request->cantidad,
        'codigo_baras'=>$request->nombre_corto,
        'tipo_consumo'=>$request->tipo_gasto,
        'id_descripcion'=>$datos_descripcion_prod->id,
        'estado'=>'A',
        'codigo_prod'=>'',
        'stock_minimo'=>$request->stock_minimo
        ]);

    $last_prod=DB::table('inventario.productos')->where('nombre_corto',$request->nombre_corto)->first();

    DB::table('inventario.productos')->update(['codigo_prod'=>$last_prod->categoria.$last_prod->marca.$last_prod->modelo.$last_prod->id]);
    $datos_prod=DB::table('inventario.productos')->where('nombre_corto',$request->nombre_corto)->first();

    //Guardar Imagen
    $img=$request->file('file');
    $extension=$img->getClientOriginalExtension();
    $path=base_path().'/public/imgproductos/';
    $nombre_img="PROD_".$datos_prod->id.".".$extension;
    Image::make($img->getRealPath())->save($nombre_img);
     // Mover Archivo
    File::move(public_path().'/'.$nombre_img,$path.$nombre_img);
    //Guardar imagen en Base de datos 
    DB::table('inventario.imagenes_productos')->insert([
        'id'=>$this->funciones->generarId(),
        'nombre'=>$nombre_img,
        'direccion'=>'/public/imgproductos/',
        'estado'=>'A',
        'producto'=>$datos_prod->id
    ]);
    $array_impuestos=json_decode($request->input('impuestos'));
    //Guardar Impuestos
    foreach ($array_impuestos as $key => $value) {
           DB::table('inventario.productos_impuestos')->insert([
            'producto'=>$datos_prod->id,
            'impuesto'=>$value->id
        ]);
    }
    // return response()->json(['respuesta' => true], 200);
    return response()->json(['respuesta' => true], 200);
    }

    public function Get_Productos(Request $request)
    {
    $currentPage = $request->pagina_actual;
    $limit = $request->limit;

    if ($request->has('filter')&&$request->filter!='') {
        $data=DB::table('inventario.productos')
                                                ->where('nombre_corto','LIKE','%'.$request->input('filter').'%')
                                                //->orwhere('descripcion','LIKE','%'.$request->input('filter').'%')
                                                ->where('estado','A')->orderBy('nombre_corto','ASC')->get();
    }else{
        $data=DB::table('inventario.productos')->where('estado','A')->orderBy('nombre_corto','ASC')->get();
    }

    foreach ($data as $key => $value) {
        //selecionar Categoria
        $data_categoria=DB::table('inventario.categorias')->select('nombre')->where('id',$value->categoria)->first();
        $value->categoria=$data_categoria->nombre;
        //selecionar Marca
        $data_categoria=DB::table('inventario.marcas')->select('nombre')->where('id',$value->marca)->first();
        $value->marca=$data_categoria->nombre;
        //selecionar Modelo
        $data_categoria=DB::table('inventario.modelos')->select('nombre')->where('id',$value->modelo)->first();
        $modelo=(count($data_categoria)!=0)?$data_categoria->nombre:'Sin-Modelo';
        $value->modelo=$modelo;
        //selecionar Descripcion
        $data_categoria=DB::table('inventario.descripcion_producto')->select('descripcion_corta')->where('id',$value->id_descripcion)->first();
        $value->descripcion_corta=$data_categoria->descripcion_corta;

        //selecionar IMPUESTOS
        $data_producto_impuesto=DB::table('inventario.productos_impuestos')->select('impuesto')->where('producto',$value->id)->first();
        //$data_impuesto=DB::table('inventario.impuestos')->select('id')->where('id',$data_producto_impuesto->impuesto)->first();
        $value->impuesto=$data_producto_impuesto->impuesto;
    }

    $data=$this->funciones->paginarDatos($data,$currentPage,$limit);
    return response()->json(['respuesta' => $data], 200);
    }

    public function Update_Productos(Request $request)
    {
    $data=DB::table('inventario.productos')->where('id',$request->id)->update(['nombre' => $request->nombre , 'descripcion' => $request->descripcion]);
    return response()->json(['respuesta' => true], 200);
    }

    public function Delete_Productos(Request $request)
    {
    $data=DB::table('inventario.productos')->where('id',$request->id)->update(['estado'=>'I']);
    return response()->json(['respuesta' => true], 200);
    }
}
