<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiningArrangements;

class DiningArrangementsController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return DiningArrangements::where($request->only(['date', 'status']))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'home_decoration_expo_id', 'date', 'breakfast_place', 'breakfast_picture',
                'lunch_place', 'lunch_picture', 'dinner_place', 'dinner_picture', 'status'
            ]);

            $data['home_decoration_expo_id'] = 1;
            $data['breakfast_picture'] = $data['breakfast_picture'][0];
            $data['lunch_picture'] = $data['lunch_picture'][0];
            $data['dinner_picture'] = $data['dinner_picture'][0];

            DiningArrangements::insert($data);
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
            return $this->conflict('已存在该地点');
        }

        return $this->created();
    }

    //导入
    public function PostImport() {
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            DiningArrangements::where('id', (int)$request->get('id', 0))->update($request->only([
                'home_decoration_expo_id', 'date', 'breakfast_place', 'breakfast_picture',
                'lunch_place', 'lunch_picture', 'dinner_place', 'dinner_picture', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('已存在该地点');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (DiningArrangements::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            };
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $diningArrangements = DiningArrangements::findOrFail((int) $request->get('id'));

        try {
            $diningArrangements->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
