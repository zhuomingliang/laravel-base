<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrafficInformation;

/*
 * 交通信息
 */
class TrafficInformationController extends Controller {
    //获取
    public function getIndex(Request $request) {
        $where = array_filter($request->only(['type']));

        $query = TrafficInformation::query();

        if (!empty($where)) {
            $query->where('type', '~', $where['type']);
        }

        $query->where('home_decoration_expo_id', HomeDecorationExpo::getCurrentId());

        return $query->where(array_filter($request->only(['status'])))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'type', 'title', 'pictures', 'status'
            ]);

            if (isset(($data['pictures'])) && is_array($data['pictures'])) {
                $data['pictures'] = str_replace(['[', ']'], ['{', '}'], json_encode($data['pictures']));
            }

            TrafficInformation::insert($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }
        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([
                'type', 'title', 'pictures', 'status'
            ]);

            if (isset(($data['pictures'])) && is_array($data['pictures'])) {
                $data['pictures'] = str_replace(['[', ']'], ['{', '}'], json_encode($data['pictures']));
            }

            TrafficInformation::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (TrafficInformation::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            }
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $model = TrafficInformation::findOrFail((int) $request->get('id'));

        try {
            $model->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
