<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TravelArrangements;

class TravelArrangementsController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return TravelArrangements::where($request->only(['date', 'status']))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'home_decoration_expo_id', 'date', 'scheduling'
            ]);

            $data['home_decoration_expo_id'] = 1;
            $data['scheduling'] = json_encode($data['scheduling']);

            TravelArrangements::insert($data);
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());
            return $this->conflict('已存在该时间');
        }

        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([
                'date', 'scheduling'
            ]);

            $data['scheduling'] = json_encode($data['scheduling']);

            TravelArrangements::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该时间');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (TravelArrangements::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            };
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $travelArrangements = TravelArrangements::findOrFail((int) $request->get('id'));

        try {
            $travelArrangements->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
