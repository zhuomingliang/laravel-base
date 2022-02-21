<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Models\Role;
use App\Http\Requests\Role\CreateOrUpdateRequest;
use App\Resources\Collection;
use App\Resources\Role as RoleResource;

class RoleController extends Controller {
    /**
     * @param Request $request
     * @return Collection
     */
    public function getIndex(Request $request) {
        return new Collection(Role::query()->where('guard_name', 'admin')->where($request->only(['name']))->paginate());
    }

    public function getDetail($id) {
        return new RoleResource(Role::query()->findOrFail($id));
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postIndex(CreateOrUpdateRequest $request) {
        Role::create($request->only([
            'name', 'admin', 'description'
        ]));

        return $this->created();
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function putIndex(CreateOrUpdateRequest $request) {
        // if (Role::where($request->only(['name', 'guard_name']))->where('id', '!=', $id)->count()) {
        //     throw RoleAlreadyExists::create($request->name, $request->guard_name);
        // }

        $role = Role::query()->findOrFail((int) $request->get('id'));

        try {
            $role->update($request->only([
                'name', 'guard_name', 'description', 'status'
            ]));
        } catch (\Exception $e) {
            throw RoleAlreadyExists::create($request->name, $request->guard_name);
        }

        return $this->noContent();
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function putStatus(Request $request) {
        $role = Role::query()->findOrFail((int) $request->get('id'));

        try {
            $role->update($request->only(['status']));
        } catch (\Exception $e) {
            throw RoleAlreadyExists::create($request->name, $request->guard_name);
        }

        return $this->noContent();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex($id) {
        Role::destroy($id);

        return $this->noContent();
    }

    /**
     * @param $id
     * @return Collection
     */
    public function getPermissions($id) {
        $role = Role::query()->findOrFail($id);

        return new Collection($role->permissions);
    }

    /**
     * Assign permission
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updatePermissions($id, Request $request) {
        $role = Role::query()->findOrFail($id);

        $role->syncPermissions($request->input('permissions', []));

        return $this->noContent();
    }
}
