<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Resources\Collection;

use App\Models\User;
use Auth;

class UserController extends Controller {


    /**
     * 返回用户列表
     *
     * @return void
     */
    public function getIndex() {
        return User::latest()->paginate(20);
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissions() {
        return new Collection(Auth::user()->getAllPermissions()->pluck('name'));
    }
}
