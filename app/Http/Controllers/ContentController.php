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
        $where = array_filter($request->only(['main_menu_id', 'sub_menu_id', 'title', 'status']));

        $query = Content::query();

        if (!empty($where['main_menu_id'])) {
            $query->where('main_menu.main_menu_id', $where['main_menu_id']);
        }

        if (!empty($where['sub_menu_id'])) {
            $query->where('sub_menu.sub_menu_id', $where['sub_menu_id']);
        }

        if (!empty($where['title'])) {
            $query->where('content.title', '~', $where['title']);
        }

        if (!empty($where['status'])) {
            $query->where('content.status', $where['status']);
        }

        return $query->join('sub_menu', 'content.sub_menu_id', '=', 'sub_menu.id')
            ->join('main_menu', 'sub_menu.main_menu_id', '=', 'main_menu.id')
            ->latest()->paginate(
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

    public function getSearch(Request $request) {
        $where = array_filter($request->only(['keyword']));
        $query = Content::query();

        if (!empty($where)) {
            $keyword = trim($where['keyword']);
            if (is_numeric($keyword)) {
                $query->where('content.id', $keyword);
            } else {
                $query->where('content.title', '~', $keyword);
            }
        }

        return $query->latest()->limit(20)->get(['id', 'title']);
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
