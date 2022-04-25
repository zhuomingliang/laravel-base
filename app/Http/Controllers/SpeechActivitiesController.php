<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/*
 * 演讲活动
 * */

class SpeechActivitiesController extends Controller
{
    //获取
    public function getIndex()
    {

    }

    //新增
    public function PostIndex()
    {
<<<<<<< HEAD
        try {
            Model::insert($request->only([
                'home_decoration_expo_id', 'title', 'date', 'time_start', 'time_end', 'place',
                'host', 'guest', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
        }
        return $this->created();
=======
>>>>>>> parent of 639a435... 演讲

    }

    //修改
    public function PutIndex()
    {

    }

    //删除
    public function DeleteIndex()
    {
<<<<<<< HEAD
        try {
            if (Model::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            }
        } catch (\Exception $e) {
        }


        return $this->unprocessableEntity();
=======

>>>>>>> parent of 639a435... 演讲
    }

    //修改状态
    public function PutStatus()
    {

    }
}
