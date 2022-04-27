<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrafficInformation as Model;

/*
 * 交通信息
 */
class TrafficInformationController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return Model::where($request->only(['created_at', 'status']))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            Model::insert($request->only([
                'type', 'title', 'pictures', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
        }
        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            Model::where('id', (int)$request->get('id', 0))->update($request->only([
                'type', 'title', 'pictures', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
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
