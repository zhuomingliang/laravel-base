<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UserController extends Controller {


    /**
     * 返回用户列表
     *
     * @return void
     */
    public function getIndex(Request $request) {
        return User::latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissions() {
        return Auth::user()->getAllPermissions()->pluck('name');
    }
}
