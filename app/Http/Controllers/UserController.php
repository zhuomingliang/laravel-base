<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Resources\Collection;

use Auth;

class UserController extends Controller {
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissions() {
        return new Collection(Auth::user()->getAllPermissions()->pluck('name'));
    }
}
