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
        return Role::where('guard_name', 'admin')->where($request->only(['name']))->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function getDetail(Request $request) {
        return new RoleResource(Role::findOrFail((int) $request->get('id')));
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postIndex(CreateOrUpdateRequest $request) {
        try {
            $menu = $request->get('menu');

            if (is_array($menu)) {
                $menu = array_filter($menu, function ($value) {
                    return is_numeric($value);
                });
            }

            Role::create($request->only([
                'name', 'guard_name', 'description', 'status'
            ]))->syncPermissions($menu);
        } catch (\Exception $e) {
            return $this->conflict('已存在该角色');
        }

        return $this->created();
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function putIndex(CreateOrUpdateRequest $request) {
        $role = Role::findOrFail((int) $request->get('id'));
        try {
            $role->update($request->only([
                'name', 'guard_name', 'description', 'status'
            ]));

            $menu = $request->get('menu');

            if (is_array($menu)) {
                $menu = array_filter($menu, function ($value) {
                    return is_numeric($value);
                });
            }

            $role->syncPermissions($menu);
        } catch (\Exception $e) {
            return $this->conflict('已存在该角色');
        }

        return $this->noContent();
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function putStatus(Request $request) {
        $role = Role::findOrFail((int) $request->get('id'));

        try {
            $role->update($request->only(['status']));
        } catch (\Exception $e) {
            return $this->conflict('已存在该角色');
        }

        return $this->noContent();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request) {
        Role::destroy((int) $request->get('id'));

        return $this->noContent();
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function getPermissions(Request $request) {
        $role = Role::findOrFail((int) $request->get('id'));

        return $role->permissions;
    }

    /**
     * Assign permission
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updatePermissions(Request $request) {
        $role = Role::findOrFail((int) $request->get('id'));

        $role->syncPermissions($request->input('permissions', []));

        return $this->noContent();
    }
}
