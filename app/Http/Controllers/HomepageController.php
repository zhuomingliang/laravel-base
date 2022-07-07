<?php

namespace App\Http\Controllers;

use App\Models\Homepage;
use Illuminate\Http\Request;

class HomepageController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request) {
        $where = array_filter($request->only(['title']));

        $query = Homepage::query();

        if (!empty($where)) {
            $query->where('homepage.title', '~', $where['title']);
        }

        $result = $query->join('sub_menu', 'homepage.sub_menu_id', '=', 'sub_menu.id')
            ->join('main_menu', 'sub_menu.main_menu_id', '=', 'main_menu.id')
            ->orderBy('homepage.module_id', 'asc')
            ->orderBy('homepage.order', 'asc')
            ->paginate(
                (int) $request->get('per_page'),
                [
                    'main_menu.id as main_menu_id',
                    'main_menu.name as main_menu',
                    'sub_menu.id as sub_menu_id',
                    'sub_menu.name as sub_menu',
                    'homepage.*',
                    \DB::raw('(select count(1) from homepage h where h.module_id = homepage.module_id) as rowspan')
                ],
                'current_page'
            )->toArray();

        $last_module_id = '';
        foreach ($result['data'] as $key => $data) {
            if ($data['rowspan'] === 0) {
                $result['data'][$key]['rowspan'] = 1;
            }

            if ($data['module_id'] === $last_module_id) {
                $result['data'][$key]['rowspan'] = 0;
            }

            $last_module_id = $data['module_id'];
        }

        return $result;
    }

    //新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([
                'module_id', 'sub_menu_id', 'status'
            ]);

            Homepage::insert($data);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return $this->conflict('新增失败');
        }

        return $this->created();
    }

    //修改
    public function PutIndex(Request $request) {
        try {
            Homepage::where('id', (int)$request->get('id', 0))->update($request->only([
                'module_id', 'sub_menu_id', 'status'
            ]));
        } catch (\Exception $e) {
            return $this->conflict('更新失败');
        }

        return $this->noContent();
    }

    //删除
    public function DeleteIndex(Request $request) {
        try {
            if (Homepage::where('id', (int)$request->get('id', 0))->delete()) {
                return $this->noContent();
            };
        } catch (\Exception $e) {
        }

        return $this->unprocessableEntity();
    }

    public function putOrder(Request $request) {
        try {
            $data = $request->only(['order']);

            $update_data = [];
            if (!empty($data['order'])) {
                $update_data['order'] = (int)$data['order'];
            }

            if (!empty($update_data)) {
                Homepage::join('sub_menu', 'homepage.sub_menu_id', '=', 'sub_menu.id')
                    ->where('module_id', $request->get('module_id', 0))
                    ->where('sub_menu.name', $request->get('sub_menu', ''))
                    ->update($update_data);
            }
        } catch (\Exception $e) {
            return $this->conflict('更新失败');
        }

        return $this->noContent();
    }

    //修改状态
    public function PutStatus(Request $request) {
        $content = Homepage::findOrFail((int) $request->get('id'));

        try {
            $content->update($request->only(['status']));
        } catch (\Exception $e) {
            return $this->conflict('更新失败');
        }

        return $this->noContent();
    }
}
