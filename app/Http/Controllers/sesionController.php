<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Extras
use config;
use DB;
use \Firebase\JWT\JWT;

class sesionController extends Controller
{
   public function Session_Status(Request $request){
        try {
            //Autenticacion
            $key=config('jwt.secret');
            $decoded = JWT::decode($request->token, $key, array('HS256'));
            return response()->json(['respuesta' => true]);
        }
        catch (\Firebase\JWT\ExpiredException $e) {
        return response()->json(['respuesta' => $e->getMessage()]);
        die();
        }
    }
}
