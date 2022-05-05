<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdvertisingVideo;

/*
 * 宣传片介绍
 */

class AdvertisingVideoController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return AdvertisingVideo::where(array_filter($request->only(['title', 'status'])))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'title', 'video', 'sort', 'status'
            ]);

            if (isset($data['video'][0])) {
                $data['video'] = $data['video'][0];
            } else {
                $data['video'] = '';
            }

            AdvertisingVideo::insert($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }
        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([
                'title', 'video', 'sort', 'status'
            ]);

            if (isset($data['video'][0])) {
                $data['video'] = $data['video'][0];
            } else {
                $data['video'] = '';
            }

            AdvertisingVideo::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (AdvertisingVideo::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            }
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $AdvertisingVideo = AdvertisingVideo::findOrFail((int) $request->get('id'));

        try {
            $AdvertisingVideo->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
