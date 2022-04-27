<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RideArrangements;

class RideArrangementsController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return RideArrangements::where(array_filter($request->only(['date', 'status'])))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'home_decoration_expo_id', 'auto_no', 'license_plate_number', 'driver',
                'driver_phone', 'commentator', 'commentator_phone', 'attendants', 'attendants_phone', 'status'
            ]);

            $data['home_decoration_expo_id'] = 1;

            RideArrangements::insert($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }

        return $this->created();
    }

    //导入
    public function PostImport() {
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([
                'auto_no', 'license_plate_number', 'driver',
                'driver_phone', 'commentator', 'commentator_phone', 'attendants', 'attendants_phone', 'status'
            ]);

            RideArrangements::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (RideArrangements::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            };
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $rideArrangements = RideArrangements::findOrFail((int) $request->get('id'));

        try {
            $rideArrangements->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
