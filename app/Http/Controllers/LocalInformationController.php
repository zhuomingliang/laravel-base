<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalInformation;

class LocalInformationController extends Controller {
    //获取
    public function getIndex(Request $request) {
        $where = array_filter($request->only(['title']));

        $query = LocalInformation::query();

        if (!empty($where)) {
            $query->where('title', '~', $where['title']);
        }

        return $query->where(array_filter($request->only(['status'])))->latest('created_at')->paginate(
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

            if (isset(($data['pictures'])) && is_array($data['pictures'])) {
                $data['pictures'] = str_replace(['[', ']'], ['{', '}'], json_encode($data['pictures']));
            }

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

            if (isset(($data['pictures'])) && is_array($data['pictures'])) {
                $data['pictures'] = str_replace(['[', ']'], ['{', '}'], json_encode($data['pictures']));
            }
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
