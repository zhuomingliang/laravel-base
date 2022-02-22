<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Permission\CreateOrUpdateRequest;
use App\Models\Permission;
use App\Resources\Collection;
use App\Resources\Permission as PermissionResource;

class PermissionController extends Controller {
    /**
     * @param Request $request
     * @return Collection
     */
    public function getIndex(Request $request) {
        $permissions = tap(Permission::latest(), function ($query) use ($request) {
            $query->where($request->only([
                'name', 'guard_name', 'pg_id'
            ]))->select(['id', 'pg_id', 'cname']);
        })->with('group')->paginate();

        return $permissions;
    }

    public function getPermissions() {
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

    /**
     * @param Request $request
     * @return PermissionResource
     */
    public function getDetail(Request $request) {
        $result = Permission::find((int)$request->get('id', 0));

        if (!$result) {
            return $this->unprocessableEntity();
        }

        return new PermissionResource($result);
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postIndex(CreateOrUpdateRequest $request) {
        try {
            \DB::table('permissions')->insert($request->only([
                'pg_id', 'cname', 'name', 'guard_name', 'icon', 'sequence', 'description'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('已存在该权限');
        }

        return $this->created();
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function putIndex(CreateOrUpdateRequest $request) {
        try {
            \DB::table('permissions')->where('id', (int)$request->get('id', 0))->update($request->only([
                'pg_id', 'cname', 'name', 'guard_name', 'icon', 'sequence', 'description'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('已存在该权限');
        }

        return $this->noContent();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request) {
        try {
            if (\DB::table('permissions')->where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            };
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }
}
