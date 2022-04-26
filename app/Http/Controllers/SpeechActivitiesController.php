<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpeechActivities;

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
            SpeechActivities::insert($request->only([
                'home_decoration_expo_id', 'title', 'date', 'time_start', 'time_end', 'place',
                'host', 'guest', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
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


            SpeechActivities::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }

        return $this->noContent();
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
        $speechActivities = SpeechActivities::findOrFail((int) $request->get('id'));

        try {
            $speechActivities->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
