<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\MainMenu;
use App\Models\SubMenu;
use App\Models\Content;
use App\Models\Homepage;

class IndexController extends Controller {
    public function index() {
        return true;
    }

    public function getMainMenu() {
        return MainMenu::where('status', true)->orderBy('order', 'asc')->get(['id', 'name']);
    }

    public function getSubMenuByMainMenuId(Request $request) {
        return SubMenu::where('main_menu_id', $request->get('id', 0))
            ->where('status', true)->orderBy('order', 'asc')->get(['id', 'name']);
    }

    public function getContentListBySubMenuId(Request $request) {
        return Content::where('sub_menu_id', $request->get('id', 0))
            ->where('content.status', true)->orderBy('content.created_at', 'desc')
            ->paginate(
                (int) $request->get('per_page'),
                ['id', 'title', 'created_at'],
                'current_page'
            );
    }

    public function getLastNContentListBySubMenuId(Request $request) {
        return Content::where('sub_menu_id', $request->get('id', 0))
            ->where('content.status', true)->orderBy('content.created_at', 'desc')
            ->limit(max($request->get('limit', 10), 20))
            ->get(
                ['id', 'title', 'created_at'],
            );
    }

    public function getLastNContentListBySubMenuIds(Request $request) {
        return Content::whereIn('sub_menu_id', explode(',', $request->get('id', 0)))
            ->where('content.status', true)
            ->orderBy('content.created_at', 'desc')
            ->limit(max($request->get('limit', 10), 20))
            ->get(
                ['sub_menu_id', 'id', 'title', 'created_at'],
            );
    }

    public function getContentById(Request $request) {
        $content = Content::where('id', $request->get('id', 0));

        $content->update(['views' => \DB::raw('"views" + 1')]);

        return $content->first(['id', 'title', 'content', 'created_at']);
    }

    public function getHomepageSubMenu(Request $request) {
        return Homepage::Join('sub_menu', 'homepage.sub_menu_id', '=', 'sub_menu.id')
            ->orderBy('homepage.module_id', 'asc')
            ->orderBy('homepage.order', 'asc')->get(['module_id', 'sub_menu_id', 'sub_menu.name as sub_menu']);
    }
}
