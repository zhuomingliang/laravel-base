<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;

class ContentController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request) {
        $where = array_filter($request->only(['title']));

        $query = Content::query();

        if (!empty($where)) {
            $query->where('content.title', '~', $where['title']);
        }

        return $query->join('sub_menu', 'content.sub_menu_id', '=', 'sub_menu.id')
            ->join('main_menu', 'sub_menu.main_menu_id', '=', 'main_menu.id')
            ->where(array_filter($request->only(['phone'])))->latest()->paginate(
                (int) $request->get('per_page'),
                [
                    'main_menu.id as main_menu_id',
                    'main_menu.name as main_menu',
                    'sub_menu.id as sub_menu_id',
                    'sub_menu.name as sub_menu',
                    'content.*'
                ],
                'current_page'
            );
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'sub_menu_id', 'title', 'content'
            ]);

            Content::insert($data);
        } catch (\Exception $e) {
            return $this->conflict('新增失败');
        }

        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            Content::where('id', (int)$request->get('id', 0))->update($request->only([
                'sub_menu_id', 'title', 'content'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('更新失败');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (Content::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            };
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $content = Content::findOrFail((int) $request->get('id'));

        try {
            $content->update($request->only(['status']));
        } catch (\Exception $e) {
            return $this->conflict('更新失败');
        }

        return $this->noContent();
    }
}
