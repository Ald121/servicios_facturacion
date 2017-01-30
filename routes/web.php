<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'cors'], function(){
	Route::post('Acceso','loginController@Acceso');
    Route::post('Get_Localizacion','localizacionController@Get_Localizacion');
                //Sesion
            Route::post('Salir','loginController@Salir');

	Route::group(['middleware' => ['jwt.auth']], function ()
        {
            //Permisos
            Route::post('Get_Permisos','PermisosController@Get_Permisos');
            Route::post('Gen_Permisos_Admin','PermisosController@Gen_Permisos_Admin');
        	//Categorias 
        	Route::post('Existencia_Categorias','categoriasController@Existencia_Categorias');
            Route::post('Add_Categorias','categoriasController@Add_Categoria');
        	Route::post('Update_Categorias','categoriasController@Update_Categoria');
        	Route::post('Delete_Categorias','categoriasController@Delete_Categoria');
        	Route::post('Get_Categorias','categoriasController@Get_Categorias');
        	//Marcas 
        	Route::post('Existencia_Marcas','MarcasController@Existencia_Marcas');
            Route::post('Add_Marcas','MarcasController@Add_Marcas');
        	Route::post('Update_Marcas','MarcasController@Update_Marcas');
        	Route::post('Delete_Marcas','MarcasController@Delete_Marcas');
        	Route::post('Get_Marcas','MarcasController@Get_Marcas');
        	//Modelos 
        	Route::post('Existencia_Modelos','ModelosController@Existencia_Modelos');
            Route::post('Add_Modelos','ModelosController@Add_Modelos');
        	Route::post('Update_Modelos','ModelosController@Update_Modelos');
        	Route::post('Delete_Modelos','ModelosController@Delete_Modelos');
        	Route::post('Get_Modelos','ModelosController@Get_Modelos');
        	//Productos 
        	Route::post('Existencia_Productos','ProductosController@Existencia_Productos');
            Route::post('Add_Productos','ProductosController@Add_Productos');
        	Route::post('Update_Productos','ProductosController@Update_Productos');
        	Route::post('Delete_Productos','ProductosController@Delete_Productos');
        	Route::post('Get_Productos','ProductosController@Get_Productos');
        	//Proveedores 
        	Route::post('Existencia_Proveedores','ProveedoresController@Existencia_Proveedores');
            Route::post('Add_Proveedores','ProveedoresController@Add_Proveedores');
        	Route::post('Update_Proveedores','ProveedoresController@Update_Proveedores');
        	Route::post('Delete_Proveedores','ProveedoresController@Delete_Proveedores');
        	Route::post('Get_Proveedores','ProveedoresController@Get_Proveedores');
            Route::post('Get_Proveedor_By_Ruc','ProveedoresController@Get_Proveedor_By_Ruc');
            //Tipos de Gastos 
            Route::post('Get_Tipo_Gastos','Tipo_GastosController@Get_Tipo_Gastos');
            Route::post('Get_Impuestos','ImpuestosController@Get_Impuestos');
            //------------------------------------------------------------------- FACTURACION -----------//
            //Clientes
            Route::post('Existencia_Clientes','ClientesController@Existencia_Clientes');
            Route::post('Add_Clientes','ClientesController@Add_Clientes');
            Route::post('Update_Clientes','ClientesController@Update_Clientes');
            Route::post('Delete_Clientes','ClientesController@Delete_Clientes');
            Route::post('Get_Clientes','ClientesController@Get_Clientes');
            Route::post('Get_By_Ruc_Ci','ClientesController@Get_By_Ruc_Ci');

            //IMPUESTOS
            Route::post('Existencia_Impuestos','ImpuestosController@Existencia_Impuestos');
            Route::post('Add_Impuestos','ImpuestosController@Add_Impuestos');
            Route::post('Update_Impuestos','ImpuestosController@Update_Impuestos');
            Route::post('Delete_Impuestos','ImpuestosController@Delete_Impuestos');
            Route::post('Get_Impuestos','ImpuestosController@Get_Impuestos');

            //FACTURAS
            Route::post('Existencia_Facturas','FacturacionController@Existencia_Facturas');
            Route::post('Add_Facturas','FacturacionController@Add_Facturas');
            Route::post('Update_Facturas','FacturacionController@Update_Facturas');
            Route::post('Delete_Facturas','FacturacionController@Delete_Facturas');
            Route::post('Get_Facturas','FacturacionController@Get_Facturas');
            //UNIDADES
            Route::post('Existencia_Unidades','UnidadesController@Existencia_Unidades');
            Route::post('Add_Unidades','UnidadesController@Add_Unidades');
            Route::post('Update_Unidades','UnidadesController@Update_Unidades');
            Route::post('Delete_Unidades','UnidadesController@Delete_Unidades');
            Route::post('Get_Unidades','UnidadesController@Get_Unidades');
	
		});
});
