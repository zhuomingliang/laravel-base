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
use App\Models\MedicalSecurity;
use App\Models\FileInformation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
class IndexController extends Controller{

    public function index(){
        return true;
    }

    //获取天气
    public function wheathInfo(){
        $wheathInfo = Cache::get('wheathInfo');
        $date = '';
        if(!empty($wheathInfo))$date = $wheathInfo['date'];//缓存时间
        $atdate = date('Ymd',time());//当前时间
        if(empty($wheathInfo) || $date != $atdate){
            header("Content-Type: text/html; charset=UTF-8");
            $URls = "http://t.weather.sojson.com/api/weather/city/101240704";
            $header[]="User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, 0);
            curl_setopt($curl, CURLOPT_URL, $URls);
            $rs = curl_exec($curl);
            $wheath = json_decode($rs,true);
            Cache::put('wheathInfo',$wheath,14400);
            return ['msg'=>'成功。','data'=>$wheath];
        }else{
            return ['msg'=>'成功！','data'=>$wheathInfo];
        }
    }

    //嘉宾签到
    public function sign(Request $request)
    {
        $data = $request->all();
        $user = $data['user'];
        $phone = $data['phone'];
        //$expo_id = (int)$data['expo_id'];
        $expo_id = HomeDecorationExpo::getCurrentId();
        $uniq_no = $data['uniq_no'];

        if(empty($user))return $this->conflict('请输入姓名');
        if(empty($phone) && strlen($phone) != 11)return $this->conflict('请输入正确的手机号码');
        if(empty($uniq_no))return $this->conflict('缺少参数uniq_no');
        $wx = $this->getOpenid($uniq_no);
        if(!isset($wx['openid']))return $this->conflict('缺少必要参数openid');
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
                GuestInformation::where(['full_name' => $user, 'phone' => $phone, 'home_decoration_expo_id' => $expo_id])->update(['uniq_no' => $wx['openid']]);
            } else {
                $gparam = [
                    'home_decoration_expo_id' => $expo_id,
                    'full_name' => $user,
                    'phone' => $phone,
                    'from' => 'APP',
                    'uniq_no' => $wx['openid'],
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
        //$expo_id = $data['expo_id'];
        $uniq_no = $data['uniq_no'];
        $expo_id = HomeDecorationExpo::getCurrentId();

        if(empty($expo_id))return $this->conflict('缺少必要参数expo_id');
        if(empty($uniq_no))return $this->conflict('缺少必要参数uniq_no');
        $wx = $this->getOpenid($uniq_no);
        if(!isset($wx['openid']))return $this->conflict('缺少必要参数openid');
        $ginnfo = GuestInformation::where(['guest_information.home_decoration_expo_id'=>$expo_id,'guest_information.uniq_no'=>$wx['openid']])
        //$ginnfo = GuestInformation::where(['guest_information.home_decoration_expo_id'=>$expo_id,'guest_information.uniq_no'=>$uniq_no])
            ->leftJoin('check_in','guest_information.id','=','check_in.guest_information_id')
            ->first();
        //是否签到
        if(!empty($ginnfo)){
            $info['is_sign'] = 1;
            $info['msg'] = '已签到';
            $info['ginnfo'] = $ginnfo;
            $info['wx'] = $wx;
            return $info;
        }else{
            $info['is_sign'] = -1;
            $info['msg'] = '未签到';
            $info['ginnfo'] = $ginnfo;
            $info['wx'] = $wx;
            return $info;
        }
    }

    //获取小程序openid
    public function getOpenid($uniq_no){

        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, "https://api.weixin.qq.com/sns/jscode2session?appid=wx09553058c0ec2dd4&secret=680f83e111926c82cef8b03522d3ace8&js_code=".$uniq_no."&grant_type=authorization_code");//输入自己的微信公众号APPID和secret
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); //不验证证书
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $wx = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        $wx = json_decode($wx,true);
        return $wx;
    }
    //最新家博会信息
    public function expo(Request $request){
        //INSERT INTO home_decoration_expo (title,description,daterange) VALUES ('第八届家博会','家博会简介内容',$$['2023-04-01 07:00:00', '2023-06-01 08:00:00']$$);
        $data = array();
        $id = HomeDecorationExpo::getCurrentId();
        if(!empty($id)){
            $data = HomeDecorationExpo::where('id','=',$id)->first()->toArray();
            $str = explode(',',$data['daterange']);
            if(strpos($str[1],')') !== false){
                $time = str_replace(')','',$str[1]);
                $str[1] = date('Y-m-d',strtotime($time)-86400);

            }
            $domain = $request->root();
            $replace = 'src="'.$domain.'/images/';
            $data['description'] = str_replace('src="images/',$replace,$data['description']);
            $data['daterange'] = str_replace('[','',$str[0]).' ~  '.$str[1];
        }
        return ['msg'=>'成功','data'=>$data];
    }

    //用餐安排
    public function diningArrangements(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        //$expo_id = $request->get('expo_id',1);
        $expo_id = HomeDecorationExpo::getCurrentId();
        if(empty($expo_id))return $this->conflict('家博会ID不能为空');
        $where[] = ['status','=',true];
        if(!empty($expo_id))
        {
            $where[] = ['home_decoration_expo_id','=',$expo_id];
        }

        $count = DiningArrangements::where($where)->count();
        if($count <= 0){
            return ['msg'=>'成功','count'=>$count, 'data'=>array()];
        }
        $daList = DiningArrangements::select(['id','home_decoration_expo_id','date','breakfast_place','breakfast_picture', 'lunch_place','lunch_picture','dinner_place','dinner_picture','status','created_at'])->where($where)->forPage($currpage, $limit)->orderBy('date','asc')->get();

       return ['msg'=>'成功','count'=>$count, 'data'=>$daList->toArray()];

    }

    //行程安排
    public function travelArrangements(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        //$expo_id = $request->get('expo_id',1);
        $expo_id = HomeDecorationExpo::getCurrentId();
        if(empty($expo_id))return $this->conflict('家博会ID不能为空');
        $where[] = ['status','=',true];
        if(!empty($expo_id))
        {
            $where[] = ['home_decoration_expo_id','=',$expo_id];
        }

        $count = TravelArrangements::where($where)->count();
        if($count <= 0){
            return ['msg'=>'成功','count'=>$count, 'data'=>array()];
        }
        $taList = TravelArrangements::select(['id','home_decoration_expo_id',
            'date','scheduling','status', 'created_at'
        ])->where($where)->forPage($currpage, $limit)->orderBy('date','asc')->get()->toArray();
        foreach ($taList as $k => $v )
        {
            $taList[$k]['scheduling'] = json_decode($v['scheduling'],true);
        }
        //var_dump($taList);exit;
        //INSERT INTO travel_arrangements (home_decoration_expo_id,date,scheduling) VALUES (1,'2022-04-25','{ "09:00": "嘉宾到场签到","10:00": "第一次演讲","11:00": "第二次演讲","14:00": "第三次演讲","15:00": "第四次演讲"}');
        //INSERT INTO travel_arrangements (home_decoration_expo_id,date,scheduling) VALUES (1,'2022-04-26','{ "09:00": "嘉宾到场签到","10:00": "第一次演讲","11:00": "第二次演讲","14:00": "第三次演讲","15:00": "第四次演讲"}');
        //INSERT INTO travel_arrangements (home_decoration_expo_id,date,scheduling) VALUES (1,'2022-04-27','{ "09:00": "嘉宾到场签到","10:00": "第一次演讲","11:00": "第二次演讲","14:00": "第三次演讲","15:00": "第四次演讲"}');
        //INSERT INTO travel_arrangements (home_decoration_expo_id,date,scheduling) VALUES (1,'2022-04-28','{ "09:00": "嘉宾到场签到","10:00": "第一次演讲","11:00": "第二次演讲","14:00": "第三次演讲","15:00": "第四次演讲"}');
        //INSERT INTO travel_arrangements (home_decoration_expo_id,date,scheduling) VALUES (1,'2022-04-29','{ "09:00": "嘉宾到场签到","10:00": "第一次演讲","11:00": "第二次演讲","14:00": "第三次演讲","15:00": "第四次演讲"}');
        //INSERT INTO travel_arrangements (home_decoration_expo_id,date,scheduling) VALUES (1,'2022-04-30','{ "09:00": "嘉宾到场签到","10:00": "第一次演讲","11:00": "第二次演讲","14:00": "第三次演讲","15:00": "第四次演讲"}');
        return ['msg'=>'成功','count'=>$count, 'data'=>$taList];
    }

    //演讲活动
    public function speechActivities(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        //$expo_id = $request->get('expo_id',1);
        $expo_id = HomeDecorationExpo::getCurrentId();
        if(empty($expo_id))return $this->conflict('家博会ID不能为空');
        $where[] = ['status','=',true];
        if(!empty($expo_id))
        {
            $where[] = ['home_decoration_expo_id','=',$expo_id];
        }

        $count = SpeechActivities::where($where)->count();
        if($count <= 0){
            return ['msg'=>'成功','count'=>$count, 'data'=>array()];
        }
        $taList = SpeechActivities::select(['id','home_decoration_expo_id',
            'title','date','start_time','end_time','place','host','guest','status','created_at'
        ])->where($where)->forPage($currpage, $limit)->orderBy('date','asc')->get();
        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //宣传片
    public function advertisingVideo(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where[] = ['status','=',true];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = AdvertisingVideo::where($where)->count();
        if($count <= 0){
            return ['msg'=>'成功','count'=>$count, 'data'=>array()];
        }
        $taList = AdvertisingVideo::select(['id','title',
            'video','sort','status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get();

        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //酒店列表
    public function hotelInformation(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where[] = ['status','=',true];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = HotelInformation::where($where)->count();
        if($count <= 0){
            return ['msg'=>'成功','count'=>$count, 'data'=>array()];
        }
        $taList = HotelInformation::select(['id','hotel',
            'address','telephone','wifi_password','breakfast_information','video',
            'liaison','liaison_phone','director','director_phone',
            'status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get()->toArray();
        foreach ($taList as $k => $v){
            $taList[$k]['vehicle_safeguard'] =  array();
            $taList[$k]['medical_security'] =  array();
            $where1 = array();
            $where2 = array();
            //车辆保障
            $where1[] = ['hotel_information_id','=',$v['id']];
            $where1[] = ['status','=',true];
            $vehicleSafeguard = VehicleSafeguard::select()->where($where1)->get();
            if(!empty($vehicleSafeguard))$taList[$k]['vehicle_safeguard'] = $vehicleSafeguard->toArray();

            //医疗保障
            $where2[] = ['hotel_information_id','=',$v['id']];
            $where2[] = ['status','=',true];
            $medicalSecurity = MedicalSecurity::select()->where($where2)->orderBy('date','asc')->get();
            if(!empty($medicalSecurity))$taList[$k]['medical_security'] = $medicalSecurity->toArray();

        }
        return ['msg'=>'成功','count'=>$count, 'data'=>$taList];
    }

    //交通信息列表
    public function trafficInformation(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where[] = ['status','=',true];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = TrafficInformation::where($where)->count();
        if($count <= 0){
            return ['msg'=>'成功','count'=>$count, 'data'=>array()];
        }
        $taList = TrafficInformation::select(['id','type',
            'title','pictures', 'status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get()->toArray();
        foreach ($taList as $k => $v )
        {
            $taList[$k]['pictures'] = explode(',',rtrim(ltrim($v['pictures'],'{'),'}'));
        }

        return ['msg'=>'成功','count'=>$count, 'data'=>$taList];
    }

    //防疫须知列表
    public function epidemicPreventionInstructions(Request $request)
    {
        $data = EpidemicPreventionInstructions::orderBy('id','desc')->first();
        if(!empty($data)){
            $data = $data->toArray();
            $domain = $request->root();
            $replace = 'src="'.$domain.'/images/';
            $data['content'] = str_replace('src="images/',$replace,$data['content']);
            return ['msg'=>'成功','data'=>$data];
        }
        return ['msg'=>'成功','data'=>array()];
    }

    //本地信息(简介)
    public function localInformation(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where[] = ['status','=',true];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = LocalInformation::where($where)->count();
        if($count <= 0){
            return ['msg'=>'成功','count'=>$count, 'data'=>array()];
        }
        $taList = LocalInformation::select(['id','title','description','pictures','status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get()->toArray();
        //INSERT INTO epidemic_prevention_instructions (content) VALUES('航空时刻表2022年XXXX航班时刻表');
        //INSERT INTO epidemic_prevention_instructions (content) VALUES('列车时刻表2022年XXXX列车时刻表');
        foreach ($taList as $k => $v )
        {
            $taList[$k]['pictures'] = explode(',',rtrim(ltrim($v['pictures'],'{'),'}'));
        }
        return ['msg'=>'成功','count'=>$count, 'data'=>$taList];
    }

    //乘车安排列表
    public function rideArrangements(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where[] = ['status','=',true];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = RideArrangements::where($where)->count();
        if($count <= 0){
            return ['msg'=>'成功','count'=>$count, 'data'=>array()];
        }
        $taList = RideArrangements::select(['id','home_decoration_expo_id','auto_no',
            'license_plate_number','driver','driver_phone','commentator','commentator_phone',
            'attendants','attendants_phone',
            'status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get();

        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //住宿安排列表
    public function accommodationArrangements(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $expo_id = HomeDecorationExpo::getCurrentId();
        if(empty($expo_id))return $this->conflict('家博会ID不能为空');
        $where[] = ['status','=',true];
        if(!empty($expo_id))
        {
            $where[] = ['home_decoration_expo_id','=',$expo_id];
        }

        $count = AccommodationArrangements::where($where)->count();
        if($count <= 0){
            return ['msg'=>'成功','count'=>$count, 'data'=>array()];
        }
        $taList = AccommodationArrangements::select(['id','home_decoration_expo_id','hotel',
            'storey_info','contacts','contact_telephone','status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get()->toArray();
        foreach ($taList as $k => $v )
        {
            $taList[$k]['storey_info'] = json_decode($v['storey_info'],true);
        }
        return ['msg'=>'成功','count'=>$count, 'data'=>$taList];
    }

    //车辆保障
    public function vehicleSafeguard(Request $request)
    {
        $currpage = (int)$request->get('currpage',1);
        $limit = (int)$request->get('limit', 10);
        $id = $request->get('id');
        $where[] =['status','=',true];
        if(!empty($id))
        {
            $where[] = ['id','=',$id];
        }

        $count = VehicleSafeguard::where($where)->count();
        if($count <= 0){
            return ['msg'=>'成功','count'=>$count, 'data'=>array()];
        }
        $taList = VehicleSafeguard::select(['id','name','phone','status','created_at'
        ])->where($where)->forPage($currpage, $limit)->get();

        return ['msg'=>'成功','count'=>$count, 'data'=>$taList->toArray()];
    }

    //PDF文件
    public function fileInformation(Request $request)
    {
        $type = (int)$request->get('type',1);
        $where[] =['status','=',true];
        switch($type){
            case 1;
                $where[] =['file_name','=','食宿及乘车安排表'];
                break;
            case 2;
                $where[] =['file_name','=','餐饮安排表'];
                break;
            case 3;
                $where[] =['file_name','=','住宿安排表'];
                break;
            case 4;
                $where[] =['file_name','like','乘车安排'];
                break;
        }
        $data = array();
        $data = FileInformation::where($where)->first();
        if(!empty($data)){
            return ['msg'=>'成功','data'=>$data->toArray()];
        }
        return ['msg'=>'成功','data'=>array()];
    }

}
