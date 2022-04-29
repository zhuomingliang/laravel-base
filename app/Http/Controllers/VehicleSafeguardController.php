<?php

namespace App\Http\Controllers;

use App\Models\VehicleSafeguard as Model;
use Illuminate\Http\Request;

/*
 * 车辆保障
 */

class VehicleSafeguardController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return Model::where(array_filter($request->only(['name', 'status'])))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            Model::insert($request->only([
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
            Model::where('id', (int)$request->get('id', 0))->update($request->only([
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
            if (Model::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            }
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $model = Model::findOrFail((int) $request->get('id'));

        try {
            $model->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
