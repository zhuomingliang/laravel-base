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

            if (!isset($tree[$path[0]])) {
                $tree[$path[0]] = ['id' => null, 'cname' => null, 'children' => null];
            }

            $tree[$path[0]]['children'][] = $permission;

            if (!isset($path[1])) {
                $tree[$path[0]]['id'] = "p_{$permission['id']}";
                $tree[$path[0]]['cname'] = $permission['cname'];
            }

            $data[$pg_id]['children'][$path[0]] = $tree[$path[0]];
        }

        $result = [];
        foreach ($data as $_data) {
            $data_children = [];

            foreach ($_data['children'] as $children) {
                usort($children['children'], function ($a, $b) {
                    return $a['id'] > $b['id'] ? 1 : -1;
                });

                $data_children[] = $children;
            }

            $_data['children'] = $data_children;
            $result[] = $_data;
        }

        return $result;
    }
}
