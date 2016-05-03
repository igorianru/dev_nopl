<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
// вывод списка
Route::get('/','MainController@index');
Route::get('/document','MainController@index');
// добавление документа
Route::get('/document/create','MainController@create');
Route::post('/document/create','MainController@create_save');
Route::post('/document/create/{r?}','MainController@create_save');
// загрузка файла
Route::post('/document/upload_file','MainController@upload_file');
// удаление файла
Route::post('/document/delete_file','MainController@delete_file');
// редатирование документа
Route::get('/document/edit/{id?}','MainController@edit');
Route::post('/document/edit/{id?}','MainController@edit_save');
Route::post('/document/edit/{id?}/{r?}','MainController@edit_save');
// удаление документа
Route::get('/document/delete/{id?}','MainController@delete');
