<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});

Route::group(['namespace'=>'App\Http\Controllers\Api'], function () {
    Route::any('index', 'IndexController@index');
    Route::any('sign', 'IndexController@sign');//嘉宾签到
    Route::any('isSign', 'IndexController@isSign');//嘉宾是否签到
    Route::any('expo', 'IndexController@expo');//最新家博会信息
    Route::any('diningArrangements', 'IndexController@diningArrangements');//用餐安排列表
    Route::any('travelArrangements', 'IndexController@travelArrangements');//行程安排列表
    Route::any('speechActivities', 'IndexController@speechActivities');//演讲活动列表
    Route::any('advertisingVideo', 'IndexController@advertisingVideo');//宣传片列表
    Route::any('hotelInformation', 'IndexController@hotelInformation');//酒店列表
    Route::any('trafficInformation', 'IndexController@trafficInformation');//交通信息列表
    Route::any('epidemicPreventionInstructions', 'IndexController@epidemicPreventionInstructions');//防疫信息列表
    Route::any('localInformation', 'IndexController@localInformation');//本地信息(简介)
    Route::any('rideArrangements', 'IndexController@rideArrangements');//乘车安排列表
    Route::any('accommodationArrangements', 'IndexController@accommodationArrangements');//住宿安排列表
    Route::any('vehicleSafeguard', 'IndexController@vehicleSafeguard');//车辆保障
});
