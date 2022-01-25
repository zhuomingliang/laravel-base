<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PermissionGroup\CreateOrUpdateRequest;
use App\Models\PermissionGroup;
use App\Resources\Collection;
use App\Resources\PermissionGroup as PermissionGroupResource;

class PermissiongroupController extends Controller {
    /**
     * @param Request $request
     * @return Collection
     */
    public function getIndex(Request $request) {
        $permissionGroups = tap(PermissionGroup::latest(), function ($query) use ($request) {
            $query->where($request->only(['name']));
        })->paginate(20);

        return $permissionGroups;
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
            'data' => $permissionGroups->toArray()
        ]);
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postIndex(CreateOrUpdateRequest $request) {
        try {
            \DB::table('permission_groups')->insert($request->only(['name']));
        } catch (\Exception $e) {
            return $this->conflict('已存在该权限组');
        }

        return $this->created();
    }

    /**
     * @param Request $request
     * @return PermissionGroupResource
     */
    public function getDetail(Request $request) {
        $result = PermissionGroup::find((int)$request->get('id', 0));

        if (!$result) {
            return $this->unprocessableEntity();
        }

        return new PermissionGroupResource($result);
    }

    /**
     * @param CreateOrUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function putIndex(CreateOrUpdateRequest $request) {
        try {
            \DB::table('permission_groups')->where('id', (int)$request->get('id', 0))->update($request->only([
                'name'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('已存在该权限');
        }

        return $this->noContent();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function deleteIndex(Request $request) {
        try {
            if (\DB::table('permission_groups')->where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            };
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }
}
