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

	Route::group(['middleware' => ['jwt.auth']], function ()
        {
        	//Categorias 
        	Route::post('Add_Categoria','caregoriasController@Add_Categoria');
        	Route::post('Update_Categoria','caregoriasController@Update_Categoria');
        	Route::post('Delete_Categoria','caregoriasController@Delete_Categoria');
        	Route::post('Get_Marcas','caregoriasController@Get_Marcas');
        	//Marcas 
        	Route::post('Add_Marcas','MarcasController@Add_Marcas');
        	Route::post('Update_Marcas','MarcasController@Update_Marcas');
        	Route::post('Delete_Marcas','MarcasController@Delete_Marcas');
        	Route::post('Get_Marcas','MarcasController@Get_Marcas');
        	//Modelos 
        	Route::post('Add_Modelos','ModelosController@Add_Modelos');
        	Route::post('Update_Modelos','ModelosController@Update_Modelos');
        	Route::post('Delete_Modelos','ModelosController@Delete_Modelos');
        	Route::post('Get_Modelos','ModelosController@Get_Modelos');
        	//Productos 
        	Route::post('Add_Productos','ProductosController@Add_Productos');
        	Route::post('Update_Productos','ProductosController@Update_Productos');
        	Route::post('Delete_Productos','ProductosController@Delete_Productos');
        	Route::post('Get_Productos','ProductosController@Get_Productos');
        	//Proveedores 
        	Route::post('Add_Proveedores','ProveedoresController@Add_Proveedores');
        	Route::post('Update_Proveedores','ProveedoresController@Update_Proveedores');
        	Route::post('Delete_Proveedores','ProveedoresController@Delete_Proveedores');
        	Route::post('Get_Proveedores','ProveedoresController@Get_Proveedores');
	
		});
});
