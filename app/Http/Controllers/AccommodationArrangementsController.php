<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccommodationArrangements;
use App\Models\HomeDecorationExpo;

class AccommodationArrangementsController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return AccommodationArrangements::where(array_filter($request->only(['hotel', 'status'])))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'home_decoration_expo_id', 'hotel', 'storey_info', 'contacts',
                'contact_telephone', 'status'
            ]);

            $data['home_decoration_expo_id'] = HomeDecorationExpo::getCurrentId();

            if ($data['home_decoration_expo_id'] === null) {
                return $this->conflict('家博会未设置为启用状态');
            }
            $data['storey_info'] = json_encode($data['storey_info']);
            AccommodationArrangements::insert($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该地点');
        }

        return $this->created();
    }

    //导入
    public function PostImport(Request $request) {
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([
                'hotel', 'storey_info', 'contacts',
                'contact_telephone', 'status'
            ]);

            $data['storey_info'] = json_encode($data['storey_info']);
            AccommodationArrangements::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该地点');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (AccommodationArrangements::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            };
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $diningArrangements = AccommodationArrangements::findOrFail((int) $request->get('id'));

        try {
            $diningArrangements->update($request->only(['status']));
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
