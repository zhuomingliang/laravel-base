<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DiningArrangements;
use App\Models\TravelArrangements;
use App\Models\SpeechActivities;
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

    //嘉宾签到
    public function sign(Request $request)
    {
        $data = $request->all();
        $user = $data['user'];
        $phone = $data['phone'];
        if(empty($user))return response()->e_back('502','请输入姓名');
        if(empty($phone) && strlen($phone) != 11)return response()->e_back('502','请输入正确的手机号码');


    }

    //最新家博会信息
    public function expo(){
        //INSERT INTO home_decoration_expo (title,description,daterange) VALUES ('第八届家博会','家博会简介内容',$$['2023-04-01 07:00:00', '2023-06-01 08:00:00']$$);
        $data = DB::table('home_decoration_expo')->orderBy('id','desc')->first();

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
        if(empty($expo_id))return response()->e_back(502, '家博会ID不能为空');
        $where = [];
        if(!empty($expo_id))
        {
            $where[] = ['home_decoration_expo_id','=',$expo_id];
        }

        $count = DiningArrangements::where($where)->count();
        $daList = DiningArrangements::select(['id','home_decoration_expo_id','date','breakfast_place','breakfast_picture', 'lunch_place','lunch_picture','dinner_place','dinner_picture','status','created_at'])->where($where)->forPage($currpage, $limit)->get();

        //INSERT INTO dining_arrangements (home_decoration_expo_id,date,breakfast_place,breakfast_picture,lunch_place,lunch_picture,dinner_place,dinner_picture) VALUES (1,'2022-04-24','南康大酒店','','南康大酒店','','南康大酒店','');
        //INSERT INTO dining_arrangements (home_decoration_expo_id,date,breakfast_place,breakfast_picture,lunch_place,lunch_picture,dinner_place,dinner_picture) VALUES (1,'2022-04-25','宝辉酒店','','宝辉酒店','','宝辉酒店','');
        //INSERT INTO dining_arrangements (home_decoration_expo_id,date,breakfast_place,breakfast_picture,lunch_place,lunch_picture,dinner_place,dinner_picture) VALUES (1,'2022-04-26','城市客厅','','城市客厅','','城市客厅','');
        //INSERT INTO dining_arrangements (home_decoration_expo_id,date,breakfast_place,breakfast_picture,lunch_place,lunch_picture,dinner_place,dinner_picture) VALUES (1,'2022-04-27','皇厨酒店','','皇厨酒店','','皇厨酒店','');

        $data = [];
        foreach ($daList as $k => $v)
        {
            $data[$k]['id']   =   $v->id;
            $data[$k]['home_decoration_expo_id']   =   $v->home_decoration_expo_id;
            $data[$k]['date']   =   $v->date;
            $data[$k]['breakfast_place']   =   $v->breakfast_place;
            $data[$k]['breakfast_picture']   =   $v->breakfast_picture;
            $data[$k]['lunch_place']   =   $v->lunch_place;
            $data[$k]['lunch_picture']   =   $v->lunch_picture;
            $data[$k]['dinner_place']   =   $v->dinner_place;
            $data[$k]['dinner_picture']   =   $v->dinner_picture;
            $data[$k]['status']   =   $v->status;
            $data[$k]['created_at']   =   $v->created_at;
        }

        return response()->s_back(100, '成功', array('count'=>$count, 'list'=>$data));
    }

    //行程安排
    public function travelArrangements(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $expo_id = $request->get('expo_id',1);
        if(empty($expo_id))return response()->e_back(502, '家博会ID不能为空');
        $where = [];
        if(!empty($expo_id))
        {
            $where[] = ['home_decoration_expo_id','=',$expo_id];
        }

        $count = TravelArrangements::where($where)->count();
        $taList = TravelArrangements::select(['id','home_decoration_expo_id',
            'date','scheduling','status', 'created_at'
        ])->where($where)->forPage($currpage, $limit)->get();

        //INSERT INTO travel_arrangements (home_decoration_expo_id,date,scheduling) VALUES (1,'2022-04-25','{ "09:00": "嘉宾到场签到","10:00": "第一次演讲","11:00": "第二次演讲","14:00": "第三次演讲","15:00": "第四次演讲"}');
        //INSERT INTO travel_arrangements (home_decoration_expo_id,date,scheduling) VALUES (1,'2022-04-26','{ "09:00": "嘉宾到场签到","10:00": "第一次演讲","11:00": "第二次演讲","14:00": "第三次演讲","15:00": "第四次演讲"}');
        //INSERT INTO travel_arrangements (home_decoration_expo_id,date,scheduling) VALUES (1,'2022-04-27','{ "09:00": "嘉宾到场签到","10:00": "第一次演讲","11:00": "第二次演讲","14:00": "第三次演讲","15:00": "第四次演讲"}');
        //INSERT INTO travel_arrangements (home_decoration_expo_id,date,scheduling) VALUES (1,'2022-04-28','{ "09:00": "嘉宾到场签到","10:00": "第一次演讲","11:00": "第二次演讲","14:00": "第三次演讲","15:00": "第四次演讲"}');
        //INSERT INTO travel_arrangements (home_decoration_expo_id,date,scheduling) VALUES (1,'2022-04-29','{ "09:00": "嘉宾到场签到","10:00": "第一次演讲","11:00": "第二次演讲","14:00": "第三次演讲","15:00": "第四次演讲"}');
        //INSERT INTO travel_arrangements (home_decoration_expo_id,date,scheduling) VALUES (1,'2022-04-30','{ "09:00": "嘉宾到场签到","10:00": "第一次演讲","11:00": "第二次演讲","14:00": "第三次演讲","15:00": "第四次演讲"}');

        $data = [];
        foreach ($taList as $k => $v)
        {
            $data[$k]['id']   =   $v->id;
            $data[$k]['home_decoration_expo_id']   =   $v->home_decoration_expo_id;
            $data[$k]['date']   =   $v->date;
            $data[$k]['scheduling']   =   $v->scheduling;
            $data[$k]['status']   =   $v->status;
            $data[$k]['created_at']   =   $v->created_at;
        }

        return response()->s_back(100, '成功', array('count'=>$count, 'list'=>$data));
    }

    //演讲活动
    public function speechActivities(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $expo_id = $request->get('expo_id',1);
        if(empty($expo_id))return response()->e_back(502, '家博会ID不能为空');
        $where = [];
        if(!empty($expo_id))
        {
            $where[] = ['home_decoration_expo_id','=',$expo_id];
        }

        $count = SpeechActivities::where($where)->count();
        $taList = SpeechActivities::select(['id','home_decoration_expo_id',
            'title','date','time_start','time_end','place','host','guest','status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get();

        //INSERT INTO speech_activities (home_decoration_expo_id,title,date,time_start,time_end,place,host,guest) VALUES(1,'主题1','2022-04-25','09:00','11:00','家具小镇','李四一','王五一');
        //INSERT INTO speech_activities (home_decoration_expo_id,title,date,time_start,time_end,place,host,guest) VALUES(1,'主题2','2022-04-26','09:00','11:00','家具小镇','李四二','王五二');
        //INSERT INTO speech_activities (home_decoration_expo_id,title,date,time_start,time_end,place,host,guest) VALUES(1,'主题3','2022-04-27','09:00','11:00','家具小镇','李四三','王五三');
        //INSERT INTO speech_activities (home_decoration_expo_id,title,date,time_start,time_end,place,host,guest) VALUES(1,'主题4','2022-04-28','09:00','11:00','家具小镇','李四四','王五四');
        //INSERT INTO speech_activities (home_decoration_expo_id,title,date,time_start,time_end,place,host,guest) VALUES(1,'主题5','2022-04-29','09:00','11:00','家具小镇','李四五','王五五');
        //INSERT INTO speech_activities (home_decoration_expo_id,title,date,time_start,time_end,place,host,guest) VALUES(1,'主题6','2022-04-30','09:00','11:00','家具小镇','李四六','王五六');

        $data = [];
        foreach ($taList as $k => $v)
        {
            $data[$k]['id']   =   $v->id;
            $data[$k]['home_decoration_expo_id']   =   $v->home_decoration_expo_id;
            $data[$k]['title']   =   $v->title;
            $data[$k]['date']   =   $v->date;
            $data[$k]['time_start']   =   $v->time_start;
            $data[$k]['time_end']   =   $v->time_end;
            $data[$k]['place']   =   $v->place;
            $data[$k]['host']   =   $v->host;
            $data[$k]['guest']   =   $v->guest;
            $data[$k]['status']   =   $v->status;
            $data[$k]['created_at']   =   $v->created_at;
        }

        return response()->s_back(100, '成功', array('count'=>$count, 'list'=>$data));
    }
}
