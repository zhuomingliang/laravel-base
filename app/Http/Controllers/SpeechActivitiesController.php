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
        return SpeechActivities::where(array_filter($request->only(['date', 'title', 'status'])))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'home_decoration_expo_id', 'date', 'start_time', 'end_time', 'title', 'place',
                'host', 'guest', 'status'
            ]);

            $data['home_decoration_expo_id'] = 1;

            SpeechActivities::insert($data);
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
        }
        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([
                'date', 'start_time', 'end_time', 'title', 'place',
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
