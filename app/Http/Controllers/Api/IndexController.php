<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\CheckIn;
use App\Models\GuestInformation;
use App\Models\HomeDecorationExpo;
use App\Models\DiningArrangements;
use App\Models\TravelArrangements;
use App\Models\SpeechActivities;
use App\Models\AdvertisingVideo;
use App\Models\HotelInformation;
use App\Models\TrafficInformation;
use App\Models\EpidemicPreventionInstructions;
use App\Models\LocalInformation;
use App\Models\RideArrangements;
use App\Models\AccommodationArrangements;
use App\Models\VehicleSafeguard;
use Illuminate\Support\Facades\Log;
class IndexController extends Controller{

    public function index(){
       return true;
    }

    //嘉宾签到
    public function sign(Request $request)
    {
        $data = $request->all();
        $user = $data['user'];
        $phone = $data['phone'];
        $expo_id = (int)$data['expo_id'];
        $uniq_no = $data['uniq_no'];
        if(empty($user))return $this->conflict('请输入姓名');
        if(empty($phone) && strlen($phone) != 11)return $this->conflict('请输入正确的手机号码');
        if(empty($uniq_no))return $this->conflict('缺少参数uniq_no');
        $ginnfo = GuestInformation::where(['guest_information.full_name'=>$user,'guest_information.phone'=>$phone,'guest_information.home_decoration_expo_id'=>$expo_id])
            ->leftJoin('check_in','guest_information.id','=','check_in.guest_information_id')
            ->first();
        DB::beginTransaction();
        try {
            //是否签到
            if (!empty($ginnfo)) {
                //新增签到记录
                $param = ['guest_information_id' => $ginnfo->id];
                CheckIn::insert($param);
                //更新小程序唯一标识
                GuestInformation::where(['full_name' => $user, 'phone' => $phone, 'home_decoration_expo_id' => $expo_id])->update(['uniq_no' => $uniq_no]);
            } else {
                $gparam = [
                    'home_decoration_expo_id' => $expo_id,
                    'full_name' => $user,
                    'phone' => $phone,
                    'from' => 'APP',
                    'uniq_no' => $uniq_no,
                ];
                $gId = GuestInformation::insertGetId($gparam, 'id');
                //新增签到记录
                $param = ['guest_information_id' => $gId];
                CheckIn::insert($param);
            }
            DB::commit();
            return ['msg'=>'成功','data'=>''];
        }catch(\Exception $e){
            log::info('嘉宾签到:'.'已存在该数据');
            DB::rollBack();
        }
        return $this->conflict('嘉宾签到失败');
    }

    //嘉宾是否签到
    public function isSign(Request $request)
    {
        $data = $request->all();
        $expo_id = $data['expo_id'];
        $uniq_no = $data['uniq_no'];
        if(empty($expo_id))return $this->conflict('缺少必要参数expo_id');
        if(empty($uniq_no))return $this->conflict('缺少必要参数uniq_no');

        $ginnfo = GuestInformation::where(['guest_information.home_decoration_expo_id'=>$expo_id,'guest_information.uniq_no'=>$uniq_no])
            ->leftJoin('check_in','guest_information.id','=','check_in.guest_information_id')
            ->first();
        //是否签到
        if(!empty($ginnfo)){
            $info['is_sign'] = 1;
            $info['msg'] = '已签到';
            return $info;
        }else{
            $info['is_sign'] = -1;
            $info['msg'] = '未签到';
            return $info;
        }
    }

    //最新家博会信息
    public function expo(){
        //INSERT INTO home_decoration_expo (title,description,daterange) VALUES ('第八届家博会','家博会简介内容',$$['2023-04-01 07:00:00', '2023-06-01 08:00:00']$$);
        $data = HomeDecorationExpo::orderBy('id','desc')->first();
        return ['msg'=>'成功','data'=>$data->toArray()];
    }

    //用餐安排
    public function diningArrangements(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $expo_id = $request->get('expo_id',1);
        if(empty($expo_id))return $this->conflict('家博会ID不能为空');
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
        return ['msg'=>'成功','count'=>$count, 'data'=>$daList->toArray()];

    }

    //行程安排
    public function travelArrangements(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $expo_id = $request->get('expo_id',1);
        if(empty($expo_id))return $this->conflict('家博会ID不能为空');
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
        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //演讲活动
    public function speechActivities(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $expo_id = $request->get('expo_id',1);
        if(empty($expo_id))return $this->conflict('家博会ID不能为空');
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

        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //宣传片
    public function advertisingVideo(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where = [];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = AdvertisingVideo::where($where)->count();
        $taList = AdvertisingVideo::select(['id','title',
            'video','sort','status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get();

        //INSERT INTO advertising_video (title,video) VALUES('视频1','视频文件');
        //INSERT INTO advertising_video (title,video) VALUES('视频2','视频文件');
        //INSERT INTO advertising_video (title,video) VALUES('视频3','视频文件');
        //INSERT INTO advertising_video (title,video) VALUES('视频4','视频文件');
        //INSERT INTO advertising_video (title,video) VALUES('视频5','视频文件');
        //INSERT INTO advertising_video (title,video) VALUES('视频6','视频文件');

        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //酒店列表
    public function hotelInformation(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where = [];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = HotelInformation::where($where)->count();
        $taList = HotelInformation::select(['id','name',
            'address','telephone','wifi_password','breakfast_information','video',
            'liaison','liaison_phone','director','director_phone',
            'status','created_at'

        ])->where($where)->forPage($currpage, $limit)->get();

        //INSERT INTO hotel_information (name,address,telephone,wifi_password,liaison,liaison_phone,director,director_phone) VALUES('南康大酒店','南康区天马山大道','0709-12345678','88888888','李四','15107970001','王五','15107970011');

        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //交通信息列表
    public function trafficInformation(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where = [];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = TrafficInformation::where($where)->count();
        $taList = TrafficInformation::select(['id','type',
            'title','pictures', 'status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get();

        //INSERT INTO traffic_information (type,title) VALUES('航空时刻表','2022年XXXX航班时刻表');
        //INSERT INTO traffic_information (type,title) VALUES('列车时刻表','2022年XXXX列车时刻表');

        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //防疫须知列表
    public function epidemicPreventionInstructions(Request $request)
    {
        $currpage = (int)$request->post('currpage',1);
        $limit = (int)$request->post('limit', 10);
        $id = $request->post('id');
        $where = [];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = EpidemicPreventionInstructions::where($where)->count();
        $taList = EpidemicPreventionInstructions::select(['id','content'
        ])->where($where)->forPage($currpage, $limit)->get();

        //INSERT INTO epidemic_prevention_instructions (content) VALUES('航空时刻表2022年XXXX航班时刻表');
        //INSERT INTO epidemic_prevention_instructions (content) VALUES('列车时刻表2022年XXXX列车时刻表');

        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //本地信息(简介)
    public function localInformation(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where = [];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = LocalInformation::where($where)->count();
        $taList = LocalInformation::select(['id','title','description','pictures','status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get();

        //INSERT INTO epidemic_prevention_instructions (content) VALUES('航空时刻表2022年XXXX航班时刻表');
        //INSERT INTO epidemic_prevention_instructions (content) VALUES('列车时刻表2022年XXXX列车时刻表');

        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //乘车安排列表
    public function rideArrangements(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where = [];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = RideArrangements::where($where)->count();
        $taList = RideArrangements::select(['id','home_decoration_expo_id','auto_no',
            'license_plate_number','driver','driver_phone','commentator','commentator_phone',
            'attendants','attendants_phone',
            'status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get();

        //INSERT INTO ride_arrangements (home_decoration_expo_id,auto_no,license_plate_number,driver,driver_phone,commentator,commentator_phone,attendants,attendants_phone) VALUES(1,'1号车','赣B125M','张三1','18574875158','张三上','18574875159','李四','18574875158');
        //INSERT INTO ride_arrangements (home_decoration_expo_id,auto_no,license_plate_number,driver,driver_phone,commentator,commentator_phone,attendants,attendants_phone) VALUES(1,'2号车','赣B126M','张三2','18574875158','张三上','18574875159','李四','18574875158');


        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //住宿安排列表
    public function accommodationArrangements(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where = [];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = AccommodationArrangements::where($where)->count();
        $taList = AccommodationArrangements::select(['id','home_decoration_expo_id','hotel',
            'storey_info','contacts','contact_telephone','status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get();

        //INSERT INTO accommodation_arrangements (home_decoration_expo_id,hotel,storey_info,contacts,contact_telephone) VALUES(1,'南康大酒店','{ "房号首位数": "1","对应楼号/层": "1","房态图": "第二次演讲"}','张三上','18574875159');
        //INSERT INTO accommodation_arrangements (home_decoration_expo_id,hotel,storey_info,contacts,contact_telephone) VALUES(1,'南康大酒店1','{ "房号首位数": "2","对应楼号/层": "2","房态图": "第二次演讲"}','张三','18574875151');


        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //车辆保障
    public function vehicleSafeguard(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where = [];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = VehicleSafeguard::where($where)->count();
        $taList = VehicleSafeguard::select(['id','name','phone','status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get();

        //INSERT INTO vehicle_safeguard (name,phone) VALUES('张三上','18574875159');
        //INSERT INTO vehicle_safeguard (name,phone) VALUES('张三','18574875151');


        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

}
