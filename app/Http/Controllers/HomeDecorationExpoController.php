<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomeDecorationExpo;

class HomeDecorationExpoController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return HomeDecorationExpo::where($request->only(array_filter(['title', 'status'])))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'daterange', 'title', 'description', 'status'
            ]);

            $data['daterange'] = '[' . implode(',', $data['daterange']) . ']';

            HomeDecorationExpo::insert($data);
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
            return $this->conflict('已存在该家博会');
        }

        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([
                'daterange', 'title', 'description', 'status'
            ]);

            $data['daterange'] = '[' . implode(',', $data['daterange']) . ']';

            HomeDecorationExpo::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该家博会');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (HomeDecorationExpo::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            };
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $model = HomeDecorationExpo::findOrFail((int) $request->get('id'));

        try {
            $model->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
