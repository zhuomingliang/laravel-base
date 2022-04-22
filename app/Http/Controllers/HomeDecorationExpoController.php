<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomeDecorationExpo;

class HomeDecorationExpoController extends Controller {
    //获取
    public function getIndex(Request $request) {
        return HomeDecorationExpo::where($request->only(['date', 'status']))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            HomeDecorationExpo::insert($request->only([
                'title', 'description', 'daterange', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('已存在该家博会');
        }

        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            HomeDecorationExpo::where('id', (int)$request->get('id', 0))->update($request->only([
                'title', 'description', 'daterange', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('已存在该家博会');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (HomeDecorationExpo::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            };
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }
}
