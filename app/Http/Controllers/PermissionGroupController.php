<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PermissionGroup\CreateOrUpdateRequest;
use App\Models\PermissionGroup;
use App\Models\Permission;
use App\Resources\Collection;
use App\Resources\PermissionGroup as PermissionGroupResource;

class PermissionGroupController extends Controller {
    /**
     * @param Request $request
     * @return Collection
     */
    public function getIndex(Request $request) {
        $permissionGroups = tap(PermissionGroup::latest(), function ($query) use ($request) {
            $query->where($request->only(['name']));
        })->paginate();

        return new Collection($permissionGroups);
    }

    /**
     * @return Collection
     */
    public function getAll() {
        $permissionGroups = PermissionGroup::latest()->get();

        return new Collection($permissionGroups);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAdminPermissions() {
        $permissionGroups = PermissionGroup::query()
            ->with(['permission' => function ($query) {
                $query->where('guard_name', 'admin');
            }])
            ->get()->filter(function ($item) {
                return count($item->permission) > 0;
            });

        return response()->json([
            'data' => array_values($permissionGroups->toArray())
        ]);
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postIndex(CreateOrUpdateRequest $request) {
        PermissionGroup::create($request->only(['name']));

        return $this->created();
    }

    /**
     * @param $id
     * @return PermissionGroupResource
     */
    public function getDetail($id) {
        return new PermissionGroupResource(PermissionGroup::findOrFail($id));
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function putIndex(CreateOrUpdateRequest $request, $id) {
        PermissionGroup::findOrFail($id)->update($request->only([
            'name'
        ]));

        return $this->noContent();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function deleteIndex($id) {
        $permissionGroup = PermissionGroup::findOrFail($id);

        if (Permission::query()->where('pg_id', $permissionGroup->id)->count()) {
            return $this->unprocessableEntity([
                'pg_id' => 'Please move or delete the vesting permission.'
            ]);
        }

        $permissionGroup->delete();

        return $this->noContent();
    }
}
