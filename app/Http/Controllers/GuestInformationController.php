<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuestInformation;
use App\Models\HomeDecorationExpo;

use App\Imports\GuestInformationImport as Import;
use Maatwebsite\Excel\Facades\Excel;

class GuestInformationController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return GuestInformation::where(array_filter($request->only(['full_name', 'phone'])))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'full_name', 'phone', 'from'
            ]);

            $data['home_decoration_expo_id'] = HomeDecorationExpo::getCurrentId();

            if ($data['home_decoration_expo_id'] === null) {
                return $this->conflict('家博会未设置为启用状态');
            }

            GuestInformation::insert($data);
        } catch (\Exception $e) {
            return $this->conflict('已存在该嘉宾');
        }

        return $this->created();
    }

    //导入
    public function PostImport(Request $request) {
        //导入方法
        try {
            Excel::import(new Import, request()->file('file'));
        } catch (\Exception $e) {
            return $this->conflict('已存在该数据');
        }
        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            GuestInformation::where('id', (int)$request->get('id', 0))->update($request->only([
                'full_name', 'phone'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('已存在该嘉宾');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (GuestInformation::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            };
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }
}
