<?php

namespace App\Http\Controllers;

use App\Models\Carsoul;
use Illuminate\Http\Request;

class CarsoulController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request) {
        $where = array_filter($request->only(['title']));

        $query = Carsoul::query();

        if (!empty($where)) {
            $query->where('title', '~', $where['title']);
        }

        $result = $query
            ->orderBy('module_id', 'asc')
            ->orderBy('order', 'asc')
            ->paginate(
                (int) $request->get('per_page'),
                [ '*' ],
                'current_page'
            )->toArray();

        foreach ($result['data'] as $key => $data) {
            if (is_numeric($data['link'])) {
                $result['data'][$key]['link'] = (int) $data['link'];
            }
        }

        return $result;
    }

    // 新增
    public function PostIndex(Request $request) {
        try {
            $data = $request->only([ 'module_id', 'image', 'title', 'link' ]);

            if (isset($data['image'][0])) {
                $data['image'] = $data['image'][0];
            }

            Carsoul::insert($data);
        } catch (\Exception $e) {
            return $this->conflict('新增失败');
        }

        return $this->created();
    }

    // 修改
    public function PutIndex(Request $request) {
        try {
            $data = $request->only([ 'module_id', 'image', 'title', 'link' ]);

            if (isset($data['image'][0])) {
                $data['image'] = $data['image'][0];
            }

            Carsoul::where('id', (int)$request->get('id', 0))->update($data);
        } catch (\Exception $e) {
            return $this->conflict('更新失败');
        }

        return $this->noContent();
    }

    // 删除
    public function DeleteIndex(Request $request) {
        try {
            if (Carsoul::where('id', (int)$request->get('id', 0))->delete()) {
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
                Carsoul::where('module_id', $request->get('module_id', 0))
                    ->where('title', $request->get('title', ''))
                    ->update($update_data);
            }
        } catch (\Exception $e) {
            return $this->conflict('更新失败');
        }

        return $this->noContent();
    }

    // 修改状态
    public function PutStatus(Request $request) {
        $content = Carsoul::findOrFail((int) $request->get('id'));

        try {
            $content->update($request->only(['status']));
        } catch (\Exception $e) {
            return $this->conflict('更新失败');
        }

        return $this->noContent();
    }
}
