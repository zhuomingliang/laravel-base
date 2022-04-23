<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuestInformation;

class GuestInformationController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return GuestInformation::where($request->only(['full_name', 'phone']))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            GuestInformation::insert($request->only([
                'full_name', 'phone', 'from'
            ]));
        } catch (\Exception $e) {
            return $this->conflict($e->getMessage());

            return $this->conflict('已存在该嘉宾');
        }

        return $this->created();
    }

    //导入
    public function PostImport(Request $request) {
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
