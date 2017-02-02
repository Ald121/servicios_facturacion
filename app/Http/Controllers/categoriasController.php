<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Funciones
use App\libs\Funciones;
//Extras
use config;
use DB;
use \Firebase\JWT\JWT;

class categoriasController extends Controller
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

    public function Existencia_Categorias(Request $request)
    {
    $datos=DB::table('inventario.categorias')->where('nombre',$request->nombre)->first();
        if (count($datos)==0) {
            return response()->json(['respuesta' => true], 200);
        }else{
            return response()->json(['respuesta' => false], 200);
        }
    }

  public function Add_Categoria(Request $request)
    {
    DB::table('inventario.categorias')->insert(['nombre' => $request->nombre, 'descripcion' => $request->descripcion, 'estado' => 'A']);
    return response()->json(['respuesta' => true], 200);
    }

    public function Get_Categorias(Request $request)
    {
    $currentPage = $request->pagina_actual;
    $limit = $request->limit;

    if ($request->has('filter')&&$request->filter!='') {
        //$data=DB::statement("SELECT * FROM inventario.tipos_categorias WHERE (nombre||descripcion) like '%".$request->input('filter')."%' and estado='A' LIMIT 5");
        $data=DB::table('inventario.categorias')
                                                ->where('nombre','LIKE','%'.$request->input('filter').'%')
                                                //->orwhere('descripcion','LIKE','%'.$request->input('filter').'%')
                                                ->where('estado','A')->orderBy('nombre','ASC')->get();
    }else{
        $data=DB::table('inventario.categorias')->where('estado','A')->orderBy('nombre','ASC')->get();
    }
    $data=$this->funciones->paginarDatos($data,$currentPage,$limit);
    return response()->json(['respuesta' => $data], 200);
    }

    public function Update_Categoria(Request $request)
    {
    $data=DB::table('inventario.categorias')->where('id',$request->id)->update(['nombre' => $request->nombre , 'descripcion' => $request->descripcion]);
    return response()->json(['respuesta' => true], 200);
    }

    public function Delete_Categoria(Request $request)
    {
    $data=DB::table('inventario.categorias')->where('id',$request->id)->update(['estado'=>'I']);
    return response()->json(['respuesta' => true], 200);
    }
}
