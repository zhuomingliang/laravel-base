<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\MainMenu;
use App\Models\SubMenu;
use App\Models\Content;

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
            ->where('status', true)->orderBy('created_at', 'desc')
            ->paginate(
                (int) $request->get('per_page'),
                ['id', 'title', 'created_at'],
                'current_page'
            );
    }

    public function getContentById(Request $request) {
        return Content::where('id', $request->get('id', 0))->first(['id', 'title', 'content', 'created_at']);
    }
}
