<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use App\Http\Requests\Permission\CreateOrUpdateRequest;
use App\Models\Permission;
use App\Resources\Collection;
use App\Resources\Permission as PermissionResource;

use Auth;

class PermissionController extends Controller {
    /**
     * @param Request $request
     * @return PermissionCollection
     */
    public function getIndex(Request $request) {
        $permissions =tap(Permission::latest(), function ($query) use ($request) {
            $query->where($request->only([
                'name', 'guard_name', 'pg_id'
            ]));
        })->with('group')->paginate();

        return new Collection($permissions);
    }

    /**
     * @param $id
     * @return PermissionResource
     */
    public function getDetail($id) {
        return new PermissionResource(Permission::query()->findOrFail($id));
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postIndex(CreateOrUpdateRequest $request) {
        $attributes = $request->only([
            'pg_id', 'cname', 'name', 'guard_name', 'icon', 'sequence', 'description'
        ]);

        $attributes['created_name'] = Auth::user()->name;

        Permission::create($attributes);

        return $this->created();
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function putIndex(CreateOrUpdateRequest $request, $id) {
        $permission = Permission::query()->findOrFail($id);

        $attributes = $request->only([
            'pg_id', 'cname', 'name', 'guard_name', 'icon', 'sequence', 'description'
        ]);

        $attributes['updated_name'] = Auth::user()->name;

        $isset = Permission::query()
            ->where(['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']])
            ->where('id', '!=', $id)
            ->count();

        if ($isset) {
            throw PermissionAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        $permission->update($attributes);

        return $this->noContent();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex($id) {
        permission::query()->findOrFail($id)->delete();

        return $this->noContent();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUserPermissions() {
        return new Collection(Auth::user()->getAllPermissions()->pluck('name'));
    }
}
