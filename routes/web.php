<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return [
        'title' => 'nifty scrapper',
        'author' => 'ahmar siddiqui',
        'email' => 'ahmar.siddiqui@gmail.com'
    ];
});
$router->get('/init', 'ScrappingController@init');
$router->get('/list', 'ScrappingController@index');
