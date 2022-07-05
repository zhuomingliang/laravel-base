<?php

namespace App\Http\Controllers;

use App\Models\MainMenu;
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
            ->where(array_filter($request->only(['phone'])))->latest('main_menu.order')->paginate(
                (int) $request->get('per_page'),
                ['main_menu.id', 'main_menu.name as main_nav', \DB::raw('(select count(1) from sub_menu where main_menu_id = main_menu.id) as rowspan'),
                 'main_menu.order as main_order', 'sub_menu.id as sub_id',
                 'sub_menu.name as sub_nav', 'sub_menu.order as sub_order',
                 'sub_menu.created_at', 'sub_menu.updated_at'],
                'current_page'
            )->toArray();

        $last_main_nav = '';
        foreach ($result['data'] as $key => $data) {
            if ($data['main_nav'] === $last_main_nav) {
                $result['data'][$key]['rowspan'] = 0;
            }

            $last_main_nav = $data['main_nav'];
        }

        return $result;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MainMenu  $mainMenu
     * @return \Illuminate\Http\Response
     */
    public function show(MainMenu $mainMenu) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MainMenu  $mainMenu
     * @return \Illuminate\Http\Response
     */
    public function edit(MainMenu $mainMenu) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MainMenu  $mainMenu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MainMenu $mainMenu) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MainMenu  $mainMenu
     * @return \Illuminate\Http\Response
     */
    public function destroy(MainMenu $mainMenu) {
        //
    }
}
