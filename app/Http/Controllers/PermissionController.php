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
            ]));
        })->with('group')->paginate();

        return new Collection($permissions);
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
