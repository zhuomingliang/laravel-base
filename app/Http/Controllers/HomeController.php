<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:admin,web'); // 允许 web 和 admin 两个 guard 访问该控制器
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        return view('home');
    }

    public function getUser(User $user) {
        $this->registerMacroHelpers();
        dd(\Auth::guard()->getGuardName(), $user);
    }

    private function registerMacroHelpers() {
        \Auth::guard()::macro('getGuardName', function () {
            return $this->name;
        });
    }
}
