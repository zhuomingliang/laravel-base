<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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


}
