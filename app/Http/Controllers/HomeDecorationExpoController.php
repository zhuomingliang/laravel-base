<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomeDecorationExpo;

class HomeDecorationExpoController extends Controller {
    //获取
    public function getIndex(Request $request) {
        $where = array_filter($request->only(['title']));

        $query = HomeDecorationExpo::query();

        if (!empty($where)) {
            $query->where('title', '~', $where['title']);
        }

        return $query->where(array_filter($request->only(['status'])))->latest()->paginate(
            (int) $request->get('per_page'),
            ['*'],
            'current_page'
        );
    }


    public function getCurrent() {
        return HomeDecorationExpo::where('status', true)->OrderBy('id', 'desc')->first();
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'daterange', 'title', 'description', 'status'
            ]);

            $data['daterange'] = '[' . implode(',', $data['daterange']) . ']';

            HomeDecorationExpo::insert($data);
        } catch (\Exception $e) {
            return $this->conflict('该家博会标题已存在，或者时间与另一个家博会时间重复');
        }

        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([
                'daterange', 'title', 'description', 'status'
            ]);

            $data['daterange'] = '[' . implode(',', $data['daterange']) . ']';

            HomeDecorationExpo::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict('该家博会标题已存在，或者时间与另一个家博会时间重复');
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

    //修改状态
    public function PutStatus(Request $request) {
        $homeDecorationExpo = HomeDecorationExpo::findOrFail((int) $request->get('id'));

        try {
            \DB::beginTransaction();

            $data = $request->only(['status']);

            if ($data['status'] === true) {
                HomeDecorationExpo::where('id', '>', 0)->update(['status' => false]);
            }
            $homeDecorationExpo->update($request->only(['status']));

            \DB::commit();
        } catch (\Exception $e) {
        }

        return $this->noContent();
    }
}
