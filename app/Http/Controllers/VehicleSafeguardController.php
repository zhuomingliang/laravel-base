<?php

namespace App\Http\Controllers;

use App\Models\VehicleSafeguard;
use Illuminate\Http\Request;

/*
 * 车辆保障
 */

class VehicleSafeguardController extends Controller {
    //获取
    public function getIndex(Request $request) {
        $where = array_filter($request->only(['name']));

        $query = VehicleSafeguard::query();

        if (!empty($where)) {
            $query->where('name', '~', $where['name']);
        }

        return $query->where(array_filter($request->only(['status'])))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            VehicleSafeguard::insert($request->only([
                'name', 'phone', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }
        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            VehicleSafeguard::where('id', (int)$request->get('id', 0))->update($request->only([
                'name', 'phone', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (VehicleSafeguard::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            }
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $model = VehicleSafeguard::findOrFail((int) $request->get('id'));

        try {
            $model->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
