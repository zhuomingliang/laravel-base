<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Permission;
use App\Resources\Collection;

class MenuController extends Controller {
    /**
     * @param Request $request
     * @return Collection
     */
    public function getIndex() {
        $permissions = Permission::from('permissions as p')->leftJoin('permission_groups as pg', 'p.pg_id', '=', 'pg.id')
            ->select(['p.id', 'p.cname', 'p.name', 'pg.id as pg_id', 'pg.name as pg_name'])->get();

        $data = [];
        $tree = [];
        foreach ($permissions as $permission) {
            $permission = $permission->toArray();
            $pg_id = $permission['pg_id'];
            $pg_name = $permission['pg_name'];
            $path = explode('/', $permission['name']);

            unset($permission['pg_id']);
            unset($permission['pg_name']);


            if (!isset($data[$pg_id])) {
                $data[$pg_id]['id'] = "pg_{$pg_id}";
                $data[$pg_id]['cname'] = $pg_name;
            }

            if (!isset($tree[$pg_id])) {
                $tree[$pg_id] = ['id' => null, 'cname' => null, 'children' => null];
            }

            if (isset($path[1])) {
                $tree[$pg_id]['children'][] = $permission;
            } else {
                $tree[$pg_id]['id'] = $permission['id'];
                $tree[$pg_id]['cname'] = $permission['cname'];
            }
            $data[$pg_id]['children'][$path[0]] = $tree[$pg_id];
        }

        $result = [];
        foreach ($data as $_data) {
            $_data['children'] = array_values($_data['children']);
            $result[] = $_data;
        }

        return $result;
    }
}
