<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\MainMenu;
use App\Models\SubMenu;
use App\Models\Content;
use App\Models\Homepage;
use App\Models\Carsoul;
use App\Models\TailNavigation;
use App\Models\Visits;

class IndexController extends Controller {
    public function index() {
        return true;
    }


    private function updateVisits() {
        Visits::upsert(
            ['date' => date("Y-m-d"), 'views' =>1],
            ['date'],
            ['views' => \DB::raw('"visits"."views" + 1')]
        );
    }

    public function getMainMenu() {
        return MainMenu::where('status', true)->orderBy('order', 'asc')->get(['id', 'name']);
    }


    public function getSubMenuByMainMenuId(Request $request) {
        return SubMenu::where('main_menu_id', $request->get('id', 0))
            ->where('status', true)->orderBy('order', 'asc')->get(['id', 'name']);
    }

    public function getContentListBySubMenuId(Request $request) {
        $this->updateVisits();

        $count = Content::where('sub_menu_id', $request->get('id', 0))
            ->where('content.status', true)->count();

        $field = ['id', 'title', 'created_at'];
        if ($count === 1) {
            $field = ['id', 'title', 'content', 'created_at'];
        }

        return Content::where('sub_menu_id', $request->get('id', 0))
            ->where('content.status', true)
            ->orderBy('content.created_at', 'desc')
            ->paginate(
                (int) $request->get('per_page'),
                $field,
                'current_page'
            );
    }

    public function getLastNContentListBySubMenuId(Request $request) {
        return Content::Join('sub_menu', 'content.sub_menu_id', 'sub_menu.id')
            ->Join('main_menu', 'sub_menu.main_menu_id', 'main_menu.id')
            ->where('sub_menu_id', $request->get('id', 0))
            ->where('content.status', true)
            ->orderBy('content.created_at', 'desc')
            ->limit(min($request->get('limit', 10), 20))
            ->get(
                ['main_menu.id as main_menu_id', 'content.sub_menu_id', 'content.id', 'content.title', 'content.created_at'],
            );
    }

    public function getLastNContentListBySubMenuIds(Request $request) {
        $this->updateVisits();

        $content = [];
        foreach (explode(',', $request->get('id', 0)) as $sub_menu_id) {
            $content[] = Content::Join('sub_menu', 'content.sub_menu_id', 'sub_menu.id')
            ->Join('main_menu', 'sub_menu.main_menu_id', 'main_menu.id')
            ->where('sub_menu_id', $sub_menu_id)
            ->where('content.status', true)
            ->select('main_menu.id as main_menu_id', 'content.sub_menu_id', 'content.id', 'content.title', 'content.created_at')
            ->orderBy('content.created_at', 'desc')
            ->limit(min($request->get('limit', 10), 20));
        }

        if (!empty($content)) {
            $query = array_pop($content);

            foreach ($content as $_query) {
                $query->unionAll($_query);
            }
        }

        return $query->get();
    }

    public function getContentById(Request $request) {
        $this->updateVisits();

        $content = Content::where('content.id', $request->get('id', 0));

        $content->update(['views' => \DB::raw('"views" + 1')]);

        $content->join('sub_menu', 'content.sub_menu_id', 'sub_menu.id')
            ->join('main_menu', 'sub_menu.main_menu_id', 'main_menu.id');
        return $content->first(
            [   'main_menu.id as main_menu_id',
                'sub_menu_id as sub_menu_id',
                'content.id',
                'content.from',
                'content.title',
                'content.content',
                'content.views',
                'content.created_at']
        );
    }

    public function getHomepageSubMenu(Request $request) {
        return Homepage::Join('sub_menu', 'homepage.sub_menu_id', '=', 'sub_menu.id')
            ->orderBy('homepage.module_id', 'asc')
            ->orderBy('homepage.order', 'asc')->get(['module_id', 'sub_menu_id', 'sub_menu.name as sub_menu']);
    }

    public function getHomepageCarsoul(Request $request) {
        return Carsoul::where('status', true)
            ->orderBy('module_id', 'asc')
            ->orderBy('order', 'asc')->get(['module_id', 'image', 'title', 'link']);
    }

    public function getTailNavigation(Request $request) {
        return TailNavigation::all(['id', 'title']);
    }

    public function getTailNavigationContentById(Request $request) {
        $this->updateVisits();

        return TailNavigation::where('id', $request->get('id', 0))->first(['id', 'title', 'content']);
    }

    public function getVisitsStatistics() {
        return \Cache::remember('VisitsStatistics', 60, function () {
            $result['total'] = Visits::sum('views');
            $result['today'] = Visits::where('date', date('Y-m-d'))->first(['date', 'views']);
            $result['yesterday'] = Visits::where('date', date('Y-m-d', strtotime("-1 day")))->first(['date', 'views']);
            $result['max'] = Visits::orderBy('views', 'desc')->first(['date', 'views']);
            $result['average'] = Visits::first([\DB::raw('avg("views")')]);

            return $result;
        });
    }

    public function getSearch(Request $request) {
        $so = scws_new();
        $so->set_charset('utf8');

        $so->send_text(trim($request->get('keyword', '')));

        $keyword = [];
        while ($tmp = $so->get_result()) {
            foreach ($tmp as $_keyword) {
                $keyword[] = $_keyword['word'];
            }
        }

        $so->close();
        $query =  Content::join('sub_menu', 'content.sub_menu_id', 'sub_menu.id')
            ->join('main_menu', 'sub_menu.main_menu_id', 'main_menu.id')
            ->where(function ($query) use ($keyword) {
                $query->whereRaw('title &@~ \'' . implode(' OR ', $keyword) . '\'')
                    ->orWhereRaw('content &@~ \'' . implode(' OR ', $keyword) . '\'');
            });

        if ($end_time = $request->get('end_time', '')) {
            $query->where('content.created_at', '>', $end_time);
        }

        try {
            return $query->paginate(
                (int) $request->get('per_page'),
                [   \DB::raw('\'' . implode(' ', $keyword) . '\' as keyword'),
                                'main_menu.id as main_menu_id',
                                'content.sub_menu_id as sub_menu_id',
                                'content.id',
                                'content.title',
                                'content.content',
                                'content.created_at'
                            ],
                'current_page'
            );
        } catch (\Exception $e) {
            return $this->noContent();
        }
    }

    public function getHotNews() {
        return Content::where('hot', true)->where('status', true)->limit(5)->get(['id', 'title', 'created_at']);
    }
}
