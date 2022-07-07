<?php

namespace App\Http\Controllers;

use App\Models\TailNavigation;
use Illuminate\Http\Request;

class TailNavigationController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request) {
        $where = array_filter($request->only(['title']));

        $query = TailNavigation::query();

        if (!empty($where)) {
            $query->where('tail_navigation.title', '~', $where['title']);
        }

        return $query->latest()->paginate(
            (int) $request->get('per_page'),
            [ 'tail_navigation.*' ],
            'current_page'
        );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'title', 'content'
            ]);

            TailNavigation::insert($data);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return $this->conflict('新增失败');
        }

        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            TailNavigation::where('id', (int)$request->get('id', 0))->update($request->only([
                'title', 'content'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('更新失败');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (TailNavigation::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            };
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $content = TailNavigation::findOrFail((int) $request->get('id'));

        try {
            $content->update($request->only(['status']));
        } catch (\Exception $e) {
            return $this->conflict('更新失败');
        }

        return $this->noContent();
    }
}
