<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalInformation;

class LocalInformationController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return LocalInformation::where($request->only(array_filter(['title', 'status'])))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'title', 'description', 'pictures', 'status'
            ]);

            $data['pictures'] = str_replace(['[', ']'], ['{', '}'], json_encode($data['pictures']));

            LocalInformation::insert($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }
        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([
                'title', 'description', 'pictures', 'status'
            ]);

            $data['pictures'] = str_replace(['[', ']'], ['{', '}'], json_encode($data['pictures']));

            LocalInformation::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (LocalInformation::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            }
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $model = LocalInformation::findOrFail((int) $request->get('id'));

        try {
            $model->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
