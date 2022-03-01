<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UserController extends Controller {


    /**
     * 返回用户列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndex(Request $request) {
        return User::from(\DB::raw('
        users,
        LATERAL (SELECT
            string_agg(r.name, \'、\') as role
        FROM
            roles r
        LEFT JOIN user_has_roles ur ON r.id = ur.role_id
        WHERE
            ur.user_id = users.id) as r
        '))->latest()->paginate(
            (int) $request->get('per_page'),
            ['users.id', 'users.username', 'users.email', 'users.created_at', 'r.*'],
            'current_page'
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissions() {
        return Auth::user()->getAllPermissions()->pluck('name');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExists(Request $request) {
        $query = User::where('username', (string) $request->get('username'));

        if ($id = (int) $request->get('id')) {
            $query->where('id', '!=', $id);
        }

        return $query->first() ? $this->conflict('用户已存在') : $this->noContent();
    }
}
