<?php

namespace App\Http\Controllers;

use App\Models\VehicleSafeguard;
use Illuminate\Http\Request;

/*
 * 车辆保障
 */
class VehicleSafeguardController extends Controller {
    //获取
    public function getIndex(Request $request) {
        $where = array_filter($request->only(['hotel_information_id', 'name']));

        $query = VehicleSafeguard::join('hotel_information', 'hotel_information.id', 'vehicle_safeguard.hotel_information_id');

        if (!empty($where)) {
            if (isset($where['hotel_information_id'])) {
                $query->where('hotel_information_id', $where['hotel_information_id']);
            }

            if (isset($where['name'])) {
                $query->where('name', '~', $where['name']);
            }
        }

        return $query->where(array_filter($request->only(['status'])))->latest('vehicle_safeguard.created_at')->paginate(
            (int) $request->get('per_page'),
            ['hotel_information.hotel', 'vehicle_safeguard.*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            VehicleSafeguard::insert($request->only([
                'hotel_information_id', 'name', 'phone', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }
        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            VehicleSafeguard::where('id', (int)$request->get('id', 0))->update($request->only([
                'hotel_information_id', 'name', 'phone', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (VehicleSafeguard::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            }
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $model = VehicleSafeguard::findOrFail((int) $request->get('id'));

        try {
            $model->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
