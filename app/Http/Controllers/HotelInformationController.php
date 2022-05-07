<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HotelInformation;

class HotelInformationController extends Controller {
    //获取
    public function getIndex(Request $request) {
        $where = array_filter($request->only(['hotel']));

        $query = HotelInformation::query();

        if (!empty($where)) {
            $query->where('hotel', '~', $where['hotel']);
        }

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
                'hotel', 'address', 'telephone', 'wifi_password', 'breakfast_information', 'video'
                , 'liaison', 'liaison_phone', 'director', 'director_phone','status'
            ]);

            if (isset($data['video'][0])) {
                $data['video'] = $data['video'][0];
            } else {
                $data['video'] = '';
            }

            HotelInformation::insert($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }
        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([
                'hotel', 'address', 'telephone', 'wifi_password', 'breakfast_information', 'video'
                , 'liaison', 'liaison_phone', 'director', 'director_phone','status'
            ]);


            if (isset($data['video'][0])) {
                $data['video'] = $data['video'][0];
            } else {
                $data['video'] = '';
            }

            HotelInformation::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (HotelInformation::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            }
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $model = HotelInformation::findOrFail((int) $request->get('id'));

        try {
            $model->update($request->only(['status']));
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }

        return $this->noContent();
    }
}
