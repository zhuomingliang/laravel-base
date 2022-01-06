<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//万能路由
// Route::group(['middleware'=>['web']], function () {
    Route::any('/{controller}/{action}', function ($controller, $action) {
        $controller = 'App\\Http\\Controllers\\' . ucfirst(strtolower($controller)) . 'Controller';

        if (class_exists($controller)) {
            $controller = \App::make($controller);

            $action = strtolower(request()->getMethod()) . ucfirst(strtolower($action));

            try {
                return \App::call([$controller, $action]);
            } catch (\ReflectionException $e) {
                if (env('APP_DEBUG') === true) {
                    throw $e;
                }
            }
        }

        return abort(404);
    }); //->where([ 'controller' => '[0-9a-zA-Z]+', 'action' => '[0-9a-zA-Z]+']);
// });
