<?php

namespace App\Http\Controllers;

use App\Models\SpeechActivities;
use App\Models\HomeDecorationExpo;

use Illuminate\Http\Request;
use App\Imports\SpeechActivitiesImport as Import;
use Maatwebsite\Excel\Facades\Excel;

/*
 * 演讲活动
 * */

class SpeechActivitiesController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return SpeechActivities::where($request->only(['date', 'status']))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'title', 'date', 'time_start', 'time_end', 'place',
                'host', 'guest', 'status'
            ]);


            $data['home_decoration_expo_id'] = HomeDecorationExpo::getCurrentId();

            if ($data['home_decoration_expo_id'] === null) {
                return $this->conflict('家博会未设置为启用状态');
            }

            SpeechActivities::insert($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }
        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([
                'title', 'date', 'time_start', 'time_end', 'place',
                'host', 'guest', 'status'
            ]);

            $data['home_decoration_expo_id'] = HomeDecorationExpo::getCurrentId();

            if ($data['home_decoration_expo_id'] === null) {
                return $this->conflict('家博会未设置为启用状态');
            }

            SpeechActivities::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该演讲');
        }

        return $this->noContent();
    }
    //导入
    public function PostImport() {
        try {
            Excel::import(new Import, request()->file('file'));
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }
        return $this->created();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (SpeechActivities::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            }
        } catch (\Exception $e) {
        }


        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $model = SpeechActivities::findOrFail((int) $request->get('id'));

        try {
            $model->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
