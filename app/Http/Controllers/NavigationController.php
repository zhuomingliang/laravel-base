<?php

namespace App\Http\Controllers;

use App\Models\MainMenu;
use App\Models\SubMenu;
use Illuminate\Http\Request;

class NavigationController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request) {
        $where = array_filter($request->only(['main_nav']));

        $query = MainMenu::query();

        if (!empty($where)) {
            $query->where('main_menu.name', '~', $where['main_nav']);
        }

        $result = $query->leftJoin('sub_menu', 'main_menu.id', '=', 'sub_menu.main_menu_id')
            ->where(array_filter($request->only(['phone'])))->orderBy('main_menu.order', 'asc')->orderBy('sub_menu.order', 'asc')->paginate(
                (int) $request->get('per_page'),
                ['main_menu.id as main_menu_id', 'main_menu.name as main_nav', \DB::raw('(select count(1) from sub_menu where main_menu_id = main_menu.id) as rowspan'),
                 'main_menu.order as main_order', 'sub_menu.id as sub_menu_id',
                 'sub_menu.name as sub_nav', 'sub_menu.order as sub_order',
                 'sub_menu.created_at', 'sub_menu.updated_at'],
                'current_page'
            )->toArray();

        $last_main_nav = '';
        foreach ($result['data'] as $key => $data) {
            if ($data['rowspan'] === 0) {
                $result['data'][$key]['rowspan'] = 1;
            }

            if ($data['main_nav'] === $last_main_nav) {
                $result['data'][$key]['rowspan'] = 0;
            }

            $last_main_nav = $data['main_nav'];
        }

        return $result;
    }

    public function getMainMenu() {
        return MainMenu::all(['id', 'name']);
    }

    public function getSubMenuByMainMenuId(Request $request) {
        return SubMenu::where('main_menu_id', $request->get('main_menu_id', 0))->get(['id', 'name']);
    }

    public function postMainMenu(Request $request) {
        try {
            $data = $request->only([ 'main_nav' ]);

            if (!empty($data['main_nav'])) {
                $data['name'] = $data['main_nav'];
                unset($data['main_nav']);

                MainMenu::insert($data);
            }
        } catch (\Exception $e) {
            return $this->conflict('已存在该导航栏');
        }

        return $this->created();
    }

    public function putMainMenu(Request $request) {
        try {
            $data = $request->only([ 'main_nav']);

            if (!empty($data['main_nav'])) {
                $data['name'] = $data['main_nav'];
                unset($data['main_nav']);

                MainMenu::where('id', (int)$request->get('main_menu_id', 0))->update($data);
            }
        } catch (\Exception $e) {
            return $this->conflict('已存在该导航栏');
        }

        return $this->noContent();
    }

    public function putMainMenu2(Request $request) {
        try {
            $data = $request->only([ 'main_nav', 'new_main_nav']);

            if (!empty($data['main_nav']) && !empty($data['new_main_nav'])) {
                $data['name'] = $data['new_main_nav'];
                $main_nav = $data['main_nav'];
                unset($data['main_nav']);
                unset($data['new_main_nav']);

                MainMenu::where('name', $main_nav)->update($data);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            return $this->conflict('已存在该导航栏');
        }

        return $this->noContent();
    }

    public function postSubMenu(Request $request) {
        try {
            $data = array_filter($request->only([ 'main_menu_id', 'sub_nav' ]));

            if (!empty($data['sub_nav'])) {
                $data['name'] = $data['sub_nav'];
                unset($data['sub_nav']);

                SubMenu::insert($data);
            }
        } catch (\Exception $e) {
            return $this->conflict('已存在该导航栏');
        }

        return $this->created();
    }

    public function putSubMenu(Request $request) {
        try {
            $data = array_filter($request->only([ 'main_menu_id', 'sub_nav' ]));

            if (!empty($data['sub_nav'])) {
                $data['name'] = $data['sub_nav'];
                unset($data['sub_nav']);

                SubMenu::where('id', (int)$request->get('sub_menu_id', 0))->update($data);
            }
        } catch (\Exception $e) {
            return $this->conflict('已存在该导航栏');
        }

        return $this->noContent();
    }

    public function putMainOrder(Request $request) {
        try {
            $data = $request->only(['main_order']);

            $update_data = [];
            if (!empty($data['main_order'])) {
                $update_data['order'] = (int)$data['main_order'];
            }

            if (!empty($update_data)) {
                MainMenu::where('name', $request->get('main_nav', ''))->update($update_data);
            }
        } catch (\Exception $e) {
            return $this->conflict('更新失败');
        }

        return $this->noContent();
    }

    public function putSubOrder(Request $request) {
        try {
            $data = $request->only(['sub_order']);

            $update_data = [];
            if (!empty($data['sub_order'])) {
                $update_data['order'] = (int)$data['sub_order'];
            }

            if (!empty($update_data)) {
                SubMenu::where('name', $request->get('sub_nav', ''))->update($update_data);
            }
        } catch (\Exception $e) {
            return $this->conflict('更新失败');
        }

        return $this->noContent();
    }
}
