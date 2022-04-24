<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DiningArrangements;

class IndexController extends Controller{

    public function index2(){
        $data = DB::table('home_decoration_expo')->get();

        if(empty($data)) return response()->e_back(500, '查询失败');
        $reData = [];
        foreach($data as $k => $v )
        {
            $reData[$k]['id'] = $v->id;
            $reData[$k]['username'] = $v->username;
            $reData[$k]['email'] = $v->email;
            $reData[$k]['email_verified_at'] = $v->email_verified_at;
            $reData[$k]['remember_token'] = $v->remember_token;
            $reData[$k]['created_at'] = $v->created_at;
            $reData[$k]['updated_at'] = $v->updated_at;
        }
        return response()->s_back(100, '查询成功',$reData);
    }

    //最新家博会信息
    public function expo(){
        //INSERT INTO home_decoration_expo (title,description,daterange) VALUES ('第八届家博会','家博会简介内容',$$['2023-04-01 07:00:00', '2023-06-01 08:00:00']$$);
        $data = DB::table('home_decoration_expo')->orderBy('id','desc')->first();
        if(empty($data)) return response()->e_back(500, '查询失败');
        $reData = [];
        $reData['id'] = $data->id;
        $reData['title'] = $data->title;
        $reData['description'] = $data->description;
        $reData['daterange'] = $data->daterange;
        $reData['status'] = $data->status;
        $reData['created_at'] = $data->created_at;
        $reData['updated_at'] = $data->updated_at;
        return response()->s_back(100, '查询成功',$reData);
    }

    //用餐安排
    public function diningArrangements(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $expo_id = $request->get('expo_id',1);
        $where = [];
        if(!empty($expo_id))
        {
            $where[] = ['home_decoration_expo_id','=',$expo_id];
        }

        $count = DiningArrangements::where($where)->count();
        $daList = DiningArrangements::select(['home_decoration_expo_id','date','breakfast_place','breakfast_picture',
            'lunch_place','lunch_picture','dinner_place','dinner_picture','status','created_at'])->where($where)->forPage($currpage, $limit)->get();
        //INSERT INTO dining_arrangements (home_decoration_expo_id,date,breakfast_place,breakfast_picture,lunch_place,lunch_picture,dinner_place,dinner_picture) VALUES (1,'2022-04-24','南康大酒店','','南康大酒店','','南康大酒店','');
        //INSERT INTO dining_arrangements (home_decoration_expo_id,date,breakfast_place,breakfast_picture,lunch_place,lunch_picture,dinner_place,dinner_picture) VALUES (1,'2022-04-25','宝辉酒店','','宝辉酒店','','宝辉酒店','');
        //INSERT INTO dining_arrangements (home_decoration_expo_id,date,breakfast_place,breakfast_picture,lunch_place,lunch_picture,dinner_place,dinner_picture) VALUES (1,'2022-04-26','城市客厅','','城市客厅','','城市客厅','');
        //INSERT INTO dining_arrangements (home_decoration_expo_id,date,breakfast_place,breakfast_picture,lunch_place,lunch_picture,dinner_place,dinner_picture) VALUES (1,'2022-04-27','皇厨酒店','','皇厨酒店','','皇厨酒店','');

        $data = [];
        foreach ($daList as $k => $cp)
        {
            $data[$k]['home_decoration_expo_id']   =   $cp->home_decoration_expo_id;
            $data[$k]['date']   =   $cp->date;
            $data[$k]['breakfast_place']   =   $cp->breakfast_place;
            $data[$k]['breakfast_picture']   =   $cp->breakfast_picture;
            $data[$k]['lunch_place']   =   $cp->lunch_place;
            $data[$k]['lunch_picture']   =   $cp->lunch_picture;
            $data[$k]['dinner_place']   =   $cp->dinner_place;
            $data[$k]['dinner_picture']   =   $cp->dinner_picture;
            $data[$k]['status']   =   $cp->status;
            $data[$k]['created_at']   =   $cp->created_at;
        }

        return response()->s_back(100, '成功', array('count'=>$count, 'list'=>$data));
    }

}
